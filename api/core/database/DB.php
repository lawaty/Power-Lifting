<?php

class DB
{
	/**
	 * Singleton DB manipulator.
	 */

	private static $instance;
	protected $db;

	public function __construct()
	{
		if (defined("DB_PATH")) {
			try {
				$this->db = new PDO('sqlite:' . DB_PATH);
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->db->exec('PRAGMA foreign_keys = ON;');
			} catch (PDOException $exception) {
				echo $exception->getMessage();
			}
		} else if (defined("DB_HOST")) {
			// Connect MYSQL Server Here
		} else
			throw new PDOException("Bad Configuration", 11);
	}

	public static function getDB(): DB
	{
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	private static function joinToString(array $array, string $conjunction, string $relation)
	{
		/**
		 * Converts a key-value array into string with specified conjunctions
		 * You can use multiple conjunctions at different places if you put them inside the key-value array as 'conjunction'=>'anything'
		 * @param $array: key-value array
		 * @param $level1_conjunction: The default conjunction to be used everywhere unless specific conjunction is found in the passed array
		 * @param $level2_conjunction: The default relation to be used everywhere unless specific conjunction is found in the passed array
		 * @return : Formatted string in the form "key1<relation>value1 <conjunction> key2<relation>value2" and so on...
		 * 
		 * @example for defaults(conjunction=', ', relation='='): joinToString(array('school'=>'engineering university', 'age'=>'20'), ', ', '=')
		 * returns 'school=?, password=?'
		 * 
		 * @example for overriding defaults(conjunction='and' , relation='>'): joinToString(array('age'=>'40', 'balance'=>'1000000', 'years_of_experience'=>'10'), 'and', '>')
		 * returns 'age > 40 and balance > 1000000 and years_of_experience > 10'
		 * 
		 * @example for adding one-time conjunctions and relations: joinToString(array('username'=>'blah', 'or', 'age'=>'20'), ', ', '=')
		 * returns 'username=? or age<=?'
		 */
		if ($conjunction != ', ')
			$conjunction = ' ' . trim($conjunction) . ' ';

		$result = '';
		$skip_conj = false;
		foreach ($array as $key => $value) {
			if (is_numeric($key)) {
				$result .= ' ' . $value . ' ';
				$skip_conj = true;
			} else {
				// Add conjunction if previous key-value exists and not skipped
				if (strlen($result) && !$skip_conj)
					$result .= $conjunction;

				$skip_conj = false;

				// in array
				if (is_array($value) && count($value)) { // Special Case
					$result .= $key . ' in (';
					foreach ($value as $i => $possibility) {
						if ($i != count($value) - 1)
							$result .= '?, ';
						else $result .= '?)';
					}
				} else $result .= $key . $relation . '?';
			}
		}
		return $result;
	}

	private static function extractParams(array $array)
	{
		$result = array();
		foreach ($array as $key => $value) {
			if (!is_numeric($key)) {
				if (is_array($value)) array_push($result, ...$value);
				else array_push($result, $value);
			}
		}

		return $result;
	}

	public function secQuery(string $query, array $params): array
	{
		/**
		 * Generic secure query function
		 * @param $query: sql query
		 * @param $params: Desired array of parameters for binding
		 * @return array containing query status and statement handle for further manipulation
		 */
		if (DEBUG) {
			echo $query . "\n";
			var_dump($params);
			echo "\n";
		}

		$stmt = $this->db->prepare($query);

		try {
			$result = $stmt->execute($params);
		} catch (PDOException $e) {
			if (str_contains($e->getMessage(), "FOREIGN"))
				throw new ForeignKeyViolated($e->getMessage());

			else if (str_contains($e->getMessage(), "UNIQUE")) {
				$conflicts = explode(',', explode("failed: ", $e->getMessage())[1]);
				foreach ($conflicts as &$conflict)
					$conflict = explode('.', $conflict)[1];
				$conflicts = implode(', ', $conflicts);

				throw new UniquenessViolated($conflicts);
			}

			throw $e;
			$result = False;
		}

		return array($result, $stmt);
	}

	public function select($fields, string $table, array $conditions = null, string $comparison = '='): array
	{
		/**
		 * Secure and easy-to-use select query
		 * @param array|string $fields: fields to fetch from each matched record
		 * @param string $table: database table to select from
		 * @param array $conditions: Matching conditions key-value pairs
		 * @return array of all matched records
		 */

		if (is_array($fields))
			$query = "select " . implode(',', $fields) . " from $table";
		else
			$query = "select $fields from $table";

		$bind_params = [];

		if (is_array($conditions) && count($conditions)) { // 
			$bind_params = DB::extractParams($conditions);
			$query .= ' where ' . DB::joinToString($conditions, 'and', $comparison);
		}
		return $this->secQuery($query, $bind_params)[1]->fetchAll(PDO::FETCH_ASSOC);
	}

	public function insert(string $table, array $fields, array $values = null)
	{
		/**
		 * Secure and easy-to-use insert query
		 * @param String $table: database table to select from
		 * @param array $fields: columns to be filled or column=>value key-value pairs
		 * @param array|null $values: values to be put at each column or null in case key-value pairs are used in the second parameter
		 * @return : id of the inserted record
		 * @return : false in case insert failed (uniqueness violated or not null condition violated etc...)
		 */

		if (!$values) {
			$values = array_values($fields);
			$fields = array_keys($fields);
		}

		$q_marks = '';
		$len = count($values);
		for ($i = 0; $i < $len; $i++) {
			if (strlen($q_marks))
				$q_marks .= ', ';
			$q_marks .= '?';
		}
		$query = 'insert into ' . $table . ' (' . implode(',', $fields) . ') values (' . $q_marks . ')';
		if (!is_array($values)) var_dump($values);
		if ($this->secQuery($query, $values)[0])
			return $this->db->lastInsertId();
		else return false;
	}

	public function update(string $table, array $new_values, array $conditions)
	{
		/**
		 * Secure and easy-to-use update query
		 * @param $table: database table to select from
		 * @param $new_values: the new column-value combinations
		 * @param $conditions: Matching conditions key-value pairs
		 * @return : number of affected rows
		 * @return : false in case insert failed (uniqueness violated or not null condition violated etc...)
		 */


		$new_values_str = DB::joinToString($new_values, ', ', '=');
		$conditions_str = ' where ' . DB::joinToString($conditions, 'and', '=');

		$query = 'update ' . $table . ' set ' . $new_values_str . $conditions_str;

		$values = array_merge(array_values($new_values), array_values($conditions));
		$result = $this->secQuery($query, $values);
		if ($result[0])
			return $result[1]->rowCount();
		else return false;
	}

	public function delete(string $table, array $conditions)
	{
		/**
		 * Secure and easy-to-use delete query
		 * @param $table: database table to delete from
		 * @param $conditions: Matching conditions key-value pairs
		 * @return : number of affected rows
		 * @return : false in case delete failed
		 */


		$cond_str = ' where ' . DB::joinToString($conditions, 'and', '=');
		$query = 'delete from ' . $table . $cond_str;
		$params = DB::extractParams($conditions);
		$result = $this->secQuery($query, $params);
		if ($result[0])
			return $result[1]->rowCount();
		else return false;
	}
}
