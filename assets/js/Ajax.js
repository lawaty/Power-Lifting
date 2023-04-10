class AJAX {
	/**
	 * Class for customizing AJAX communications by setting default callback functions for ajax requests for improving consistency, centralization, and simplicity.
   * All default callbacks can be simply overriden by specifying new ones on calling AJAX.ajax({...}) just as $.ajax({...})
	 */

	static beforeSend(){
    /**
     * Function that is called before sending ajax requests by default
     */
    // console.log("Request is about to start")
	}

	static complete(xhr, callback) {
		/**
		 * Function that is called after ajax requests are completed by default (called for both success and error status)
		 */

		try {
			xhr.parsed = JSON.parse(xhr.responseText)
		} catch (error) {
			xhr.parsed = {}
		}

		if(typeof(callback) == "function")
			callback(xhr)
	}

	static error(e, options){
    /**
     * Function catches the request failure
     */
    console.log("Fail:", options.data, e.status, e.responseText)
	}

	static success(xhr, callback) {
		/**
		 * Function that handles the success status
		 */

		try {
			xhr.parsed = JSON.parse(xhr.responseText)
		} catch (error) {
			xhr.parsed = {}
		}

		if(callback)
			callback(xhr)
		
	}

	static ajax(options) {
		/**
		 * options must contain url, type, data functions
		 */

		if (options.data instanceof FormData) {
			options.contentType = false;
			options.processData = false;
		}
		
		if (options.beforeSend === undefined)
			options.beforeSend = AJAX.beforeSend()

		let callback = options.complete
		options.complete = function (xhr, status) {
			AJAX.complete(xhr, callback)
		}
			
		let success_callback = options.success
		options.success = function (blah1, blah2, xhr) {
			AJAX.success(xhr, success_callback)
		}

		return $.ajax(options)
	}
}