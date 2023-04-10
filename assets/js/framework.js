// Date and Time
// =============

/**
 * Ndate extends the built-in Date object to add some utility methods.
 * 
 * @class
 */
class Ndate extends Date {
  /**
   * A subclass of the built-in Date class with additional functionality.
   * 
   * @class
   */

  /**
   * Adds a given number of days to the current date and returns the updated date object.
   * 
   * @param {number} num_of_days - The number of days to add.
   * @returns {Ndate} The updated date object.
   */
  addDays(num_of_days) {
    this.setDate(this.getDate() + num_of_days);
    return this;
  }

  /**
   * Adds a given number of weeks to the current date and returns the updated date object.
   * 
   * @param {number} num_of_weeks - The number of weeks to add.
   * @returns {Ndate} The updated date object.
   */
  addWeeks(num_of_weeks) {
    this.setDate(this.getDate() + num_of_weeks * 7);
    return this;
  }

  /**
   * Adds a given number of months to the current date and returns the updated date object.
   * 
   * @param {number} num_of_months - The number of months to add.
   * @returns {Ndate} The updated date object.
   */
  addMonths(num_of_months) {
    this.setMonth(this.getMonth() + num_of_months);
    this.setDate(1);
    return this;
  }

  /**
   * Returns a string representation of the date in ISO format (YYYY-MM-DD).
   * 
   * @returns {string} The date in ISO format.
   */
  toString() {
    const offset = this.getTimezoneOffset();
    let temp = new Ndate(this.getTime() - (offset * 60 * 1000));
    return temp.toISOString().split('T')[0];
  }

  /**
   * Calculates the difference between the current date and the given date and returns an NInterval object representing the duration of the difference.
   * 
   * @param {Ndate} date - The date to calculate the difference from.
   * @returns {NInterval} An NInterval object representing the duration of the difference.
   */
  diff(date) {
    return new NInterval({ millis: Math.abs(date - this) });
  }

  /**
   * Returns the index of the first day of the month (0-6) of the current date.
   * 
   * @returns {number} The index of the first day of the month.
   */
  firstDayOfMonth() {
    return new Ndate(this.getFullYear(), this.getMonth(), 1).getDay();
  }

  /**
   * Returns the last day of the month of the current date.
   * 
   * @returns {number} The last day of the month.
   */
  lastDayOfMonth() {
    return new Ndate(this.getFullYear(), this.getMonth() + 1, 0).getDate();
  }

  /**
   * Checks if the given date is the same as the current date.
   * 
   * @param {Ndate} date - The date to compare to.
   * @returns {boolean} True if the dates are the same, false otherwise.
   */
  sameDate(date) {
    return date.getFullYear() == this.getFullYear() &&
      date.getMonth() == this.getMonth() &&
      date.getDate() == this.getDate();
  }
}

const SEC_PER_MIN = 60;

const MS_PER_SEC = 1000;
const MS_PER_MIN = MS_PER_SEC * 60;
const MS_PER_HOUR = MS_PER_MIN * 60;
const MS_PER_DAY = MS_PER_HOUR * 24;

const MINS_PER_HOUR = 60;
const SEC_PER_HOUR = MINS_PER_HOUR * 60;

/**
 * A class that represents a duration of time, with properties for the duration in milliseconds, seconds,
 * minutes, hours, and days, and methods to format the duration as a clock string and construct a new
 * `NInterval` from a clock string.
 *
 * @class NInterval
 */
class NInterval {
  /**
   * Creates a new `NInterval` instance from an object with one of the following properties:
   * - `millis`: the duration in milliseconds
   * - `secs`: the duration in seconds
   * - `mins`: the duration in minutes
   *
   * @constructor
   * @param {Object} object - The object used to initialize the `NInterval` instance
   * @throws {Error} If the `object` parameter does not have a valid property
   */
  constructor(object) {
    if (object.hasOwnProperty('millis'))
      this.millis = object.millis;
    else if (object.hasOwnProperty('secs'))
      this.millis = object.secs * MS_PER_SEC;
    else if (object.hasOwnProperty('mins'))
      this.millis = object.mins * MS_PER_MIN;
    else
      throw new Error("Weird Input to NInterval: found" + object)

    this.total_millis = this.millis;

    this.days = parseInt(this.millis / MS_PER_DAY);
    this.millis -= this.days * MS_PER_DAY;

    this.hrs = parseInt(this.millis / MS_PER_HOUR);
    this.millis -= this.hrs * MS_PER_HOUR;

    this.mins = parseInt(this.millis / MS_PER_MIN);
    this.millis -= this.mins * MS_PER_MIN;

    this.secs = parseInt(this.millis / MS_PER_SEC);

    this.millis -= this.secs * MS_PER_SEC;
  }

  /**
   * Returns a formatted clock string representing the duration of this `NInterval` instance.
   * The string has the format "HH:MM:SS", where "HH" is the number of hours, "MM" is the number of minutes,
   * and "SS" is the number of seconds.
   *
   * @method formatClock
   * @param {Object} [object=null] - An optional object used to customize the output.
   * @returns {string} A formatted clock string representing the duration of this `NInterval` instance
   */
  formatClock(object = null) {
    let hours = this.hrs < 10 ? '0' + this.hrs : this.hrs
    let minutes = this.mins < 10 ? '0' + this.mins : this.mins
    let seconds = this.secs < 10 ? '0' + this.secs : this.secs

    return `${hours}:${minutes}:${seconds}`
  }

  /**
   * Creates a new `NInterval` instance from a clock string with the format "HH:MM:SS",
   * where "HH" is the number of hours, "MM" is the number of minutes, and "SS" is the number of seconds.
   *
   * @static
   * @method fromClock
   * @param {string} clock - A clock string with the format "HH:MM:SS"
   * @returns {NInterval | null} A new `NInterval` instance representing the duration of the clock string
   */
  static fromClock(clock) {
    let regex = Regex.TIME
    if (!regex.test(clock)) {
      try {
        throw new Error("Invalid Clock Format")
      } catch (error) {
        console.warn(error.stack)
        return null;
      }
    }

    let seconds = parseInt(clock.split(':')[0]) * SEC_PER_HOUR + parseInt(clock.split(':')[1]) * SEC_PER_MIN + parseInt(clock.split(':')[2])
    return new NInterval({ secs: seconds })
  }

  /**
   * Returns the total time in the specified unit.
   *
   * @param {string} unit - The unit to calculate the total time for. Valid values are "hrs", "mins", and "secs".
   * @returns {number} The total time in the specified unit, or -1 if the unit is invalid.
   */
  total(unit) {
    switch (unit) {
      case 'hrs':
        return parseInt(this.hrs)
      case 'mins':
        return parseInt(this.mins + this.hrs * 60)
      case 'secs':
        return parseInt(this.secs + this.mins * 60 + this.hrs * 3600)
      default:
        return -1;
    }
  }
}
/* ---------------------------------------------------------------------- */

// Regex MACROS
// ============

/**
 * An object containing regular expressions for common validation purposes.
 * @typedef {Object} Regex
 * @property {RegExp} INT - Regular expression to validate an integer.
 * @property {RegExp} SINT - Regular expression to validate a signed integer.
 * @property {RegExp} ZERO_ONE - Regular expression to validate a zero or one value.
 * @property {RegExp} MONTH - Regular expression to validate a month (1-12).
 * @property {RegExp} DATE - Regular expression to validate a date in the format YYYY-MM-DD.
 * @property {RegExp} TIME - Regular expression to validate a time in the format HH:MM.
 * @property {RegExp} DATE_TIME - Regular expression to validate a date and time in the format YYYY-MM-DD HH:MM.
 * @property {RegExp} DATE_TIME_SEC - Regular expression to validate a date and time with seconds in the format YYYY-MM-DD HH:MM:SS.
 * @property {RegExp} DAY_TIME - Regular expression to validate a day and time in the format "Day HH:MM", where Day is one of Sun, Mon, Tue, Wed, Thu, Fri, Sat.
 * @property {RegExp} JWT - Regular expression to validate a JWT token.
 * @property {RegExp} LOGIN - Regular expression to validate a login string with Arabic script, letters, numbers, hyphens, and spaces.
 * @property {RegExp} EMAIL - Regular expression to validate an email address.
 * @property {RegExp} NAME - Regular expression to validate a name with Arabic script, letters, spaces.
 * @property {RegExp} PHONE - Regular expression to validate a phone number.
 * @property {RegExp} GRADE - Regular expression to validate a grade (1-12).
 * @property {RegExp} GENERIC - Regular expression to validate any string with Arabic script, letters, numbers, hyphens, parentheses, spaces.
 * @property {RegExp} ANY - Regular expression to validate any string.
 */
const Regex = {
  INT: /^[0-9]*$/,
  SINT: /^-?[0-9]*$/,
  ZERO_ONE: /^[0-1]$/,

  MONTH: /^([1-9]|1[0-2])$/,
  DATE: /^(\d{4})-(\d{2})-(\d{2})$/,
  TIME: /^[0-9]{2}:[0-9]{2}:[0-9]{2}$/,
  DATE_TIME: /^(\d{4})-(\d{2})-(\d{2}) [0-9]{2}:[0-9]{2}$/,
  DATE_TIME_SEC: /^(\d{4})-(\d{2})-(\d{2}) [0-9]{2}:[0-9]{2}:[0-9]{2}$/,
  DAY_TIME: /^(Sun|Mon|Tue|Wed|Thu|Fri|Sat) [0-9]{2}:[0-9]{2}$/,

  JWT: /^[\w-]*\.[\w-]*\.[\w-]*$/,
  LOGIN: /^[\p{Script=Arabic}\u0020-\u007E\w\-. ]*$/gu,
  EMAIL: /^[\w_\-.]+@[\w]+\.[\w]*$/,
  NAME: /^[\p{Script=Arabic}\u0020-\u007E\w ]*$/gu,
  PHONE: /^\+?[0-9]+$/,
  GRADE: /^([1-9]|1[0-2])$/,
  GENERIC: /^[\p{Script=Arabic}\u0020-\u007E\w_\-\)\(.+\s]*$/gu,
  ANY: /^.*$/,
}

/* ---------------------------------------------------------------------- */
// LocalStorage Wrapper
// ====================

let local = {
  /**
   * Retrieves a value from localStorage given its key.
   *
   * @memberof local
   * @function get
   * @param {string} key - The key of the value to retrieve.
   * @returns {string|false} - The value associated with the key, or false if the key doesn't exist.
   */
  get: function (key) {
    let result = localStorage.getItem(key)
    if (result !== null)
      return result
    return false
  },

  /**
   * Stores a key-value pair in localStorage.
   *
   * @memberof local
   * @function set
   * @param {string} key - The key of the value to store.
   * @param {string} value - The value to store.
   */
  set: function (key, value) {
    localStorage.setItem(key, value)
  },

  /**
   * Removes a value from localStorage given its key.
   *
   * @memberof local
   * @function remove
   * @param {string} key - The key of the value to remove.
   */
  remove: function (key) {
    localStorage.removeItem(key)
  }
};

/* ---------------------------------------------------------------------- */

// Sync Manager
// ============

class MissingInfo extends Error {
  constructor(type, missing, object) {
    super()
    this.name = 'MissingInfo'
    this.message = `'${missing}' is required for object of type '${type}'. Found in this object: ${JSON.stringify(object)}`
  }
}

class TypeNotFound extends Error {
  constructor(type) {
    super()
    this.name = 'TypeNotFound'
    this.message = `'${type}' is not registered`
  }
}

class PropertyDoesNotExist extends Error {
  constructor(type, property) {
    super()
    this.name = 'PropertyDoesNotExist'
    this.message = `'${property}' does not exist for resource of type ${type}`
  }
}

class ResourceNotFound extends Error {
  constructor(type, key) {
    super()
    this.name = 'ResourceNotFound'
    this.message = `Key '${key}' is not attached to any resource of type ${type}`
  }
}

/**
 * A JavaScript object that manages the synchronization of objects.
 * @type {syncManager}
 */
let syncManager = {
  types: {},

  /**
   * 
   * @param {string} type 
   * @param {Array<string>} fields 
   */
  registerType: function (type, fields) {
    this.types[type] = fields
    this[type] = {}
  },

  /**
   * returns an object with type and key
   * @param {string} type 
   * @param {string|int} key 
   * @returns {any}
   */
  get: function (type, key) {
    if (this.validateType(type))
      return undefined

    return this[type][key]
  },

  /**
   * adds new resource with type and key
   * @param {string} type 
   * @param {object} object 
   * @param {string|int} key 
   * @returns {boolean}
   */
  add: function (type, object, key = null) {
    if (this.validateType(type))
      return false

    if (key === null)
      key = object[this.types[type][0]]

    if (key === undefined) {
      try {
        throw new MissingInfo(type, this.types[type][0], object)
      } catch (error) {
        console.warn(error.stack)
      }
    }

    let healthy_object = {}
    for (let required of this.types[type]) {
      if (object[required] === undefined) {
        try {
          throw new MissingInfo(type, required, object)
        } catch (error) {
          console.warn(error.stack)
        }
      }

      healthy_object[required] = object[required]
    }

    $(document).trigger(`${type}-changed`)
    $(document).trigger(`${type}-added`, healthy_object)

    this[type][key] = healthy_object
    return true;
  },

  /**
   * edits single parameter of a certain object with type, key, and propertyname
   * @param {string} type 
   * @param {string|int} key 
   * @param {string|int} property 
   * @param {any} value 
   * @returns {boolean}
   */
  edit: function (type, key, property, value) {
    if (this.validateType(type) || this.validateKey(type, key) || this.validateProperty(type, property))
      return false;

    this[type][key][property] = value;

    $(document).trigger(`${type}-changed`)
    $(document).trigger(`${type}-edited`, this[type][key])

    return true;
  },

  /**
   * removes an object with type and key
   * @param {string} type 
   * @param {string|int} key 
   * @returns {boolean}
   */
  remove: function (type, key) {
    if (this.validateType(type) || this.validateKey(type, key))
      return false;

    $(document).trigger(`${type}-changed`)
    $(document).trigger(`${type}-removed`, this[type][key])

    delete this[type][key]

    if (this.isEmpty(type))
      $(document).trigger(`${type}-empty`)

    return true;
  },

  /**
   * Deletes all resources of given type
   * @param {string} type 
   */
  empty(type) {
    let iterable = this[type]
    for (let key in iterable)
      this.remove(type, key)
  },

  /**
   * @param {string} type 
   * @returns {boolean}
   */
  isEmpty(type) {
    return Object.keys(this[type]).length == 0;
  },

  // Validators
  validateType(type) {
    if (this[type] === undefined) {
      try {
        throw new TypeNotFound(type)
      } catch (error) {
        console.warn(error.stack)
        return true;
      }
    }
    return false;
  },

  validateProperty(type, property) {
    if (!this['types'][type].includes(property)) {
      try {
        throw new PropertyDoesNotExist(type, property)
      } catch (error) {
        console.warn(error.stack)
        return true;
      }
    }
    return false;
  },

  validateKey(type, key) {
    if (this[type][key] === undefined) {
      try {
        throw new ResourceNotFound(type, key)
      } catch (error) {
        console.warn(error.stack)
        return true;
      }
    }
    return false;
  }
}

/** Dependency Management */
function loadTemplate(template) {
  $.ajax({
    type: "GET",
    url: `${TEMPLATES}/${template}/.html`,
    success: function (html) {
      $('[template="' + template + '"]').html(html);
      $("head").append(`<link rel="stylesheet" href="${TEMPLATES}/${template}/.css">`)
      $("body").append(`<script src="${TEMPLATES}/${template}/.js"></script>`)
    }
  });
}