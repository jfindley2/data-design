<?php
namespace Edu\Cnm\Dmcdonald21\DataDesign;

/**
 * Cross Section of a Twitter Profile
 *
 * This is a cross section of what is probably stored about a Twitter user. This entity is a top level entity that
 * holds the keys to the other entities in this example (i.e., Favorite and Profile).
 *
 * @author Dylan McDonald <dmcdonald21@cnm.edu>
 * @version 2.0.0
 **/
class Profile implements \JsonSerializable {
	/**
	 * id for this Profile; this is the primary key
	 * @var int $profileId
	 **/
	private $profileId;
	/**
	 * at handle for this Profile; this is a unique index
	 * @var string $atHandle
	 **/
	private $atHandle;
	/**
	 * email for this Profile; this is a unique index
	 * @var string $email
	 **/
	private $email;
	/**
	 * phone number for this Profile
	 * @var string $phone
	 **/
	private $phone;

	/**
	 * constructor for this Profile
	 *
	 * @param int|null $newProfileId id of this Profile or null if a new Profile
	 * @param string $newAtHandle string containing newAtHandle
	 * @param string $newEmail string containing email
	 * @param string $newPhone string containing phone number
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 **/
	public function __construct(int $newProfileId = null, string $newAtHandle, string $newEmail, string $newPhone) {
		try {
			$this->setProfileId($newProfileId);
			$this->setAtHandle($newAtHandle);
			$this->setEmail($newEmail);
			$this->setPhone($newPhone);
		} catch(\InvalidArgumentException $invalidArgument) {
			// rethrow the exception to the caller
			throw(new \InvalidArgumentException($invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(\RangeException $range) {
			// rethrow the exception to the caller
			throw(new \RangeException($range->getMessage(), 0, $range));
		} catch(\TypeError $typeError) {
			// rethrow the exception to the caller
			throw(new \TypeError($typeError->getMessage(), 0, $typeError));
		} catch(\Exception $exception) {
			// rethrow the exception to the caller
			throw(new \Exception($exception->getMessage(), 0, $exception));
		}
	}

	/**
	 * accessor method for profile id
	 *
	 * @return int|null value of profile id (or null if new Profile)
	 **/
	public function getProfileId() {
		return($this->profileId);
	}

	/**
	 * mutator method for profile id
	 *
	 * @param int|null $newProfileId value of new profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setProfileId(int $newProfileId = null) {
		// base case: if the tweet id is null, this a new tweet without a mySQL assigned id (yet)
		if($newProfileId === null) {
			$this->profileId = null;
			return;
		}

		// verify the profile id is positive
		if($newProfileId <= 0) {
			throw(new \RangeException("profile id is not positive"));
		}

		// convert and store the profile id
		$this->profileId = intval($newProfileId);
	}

	/**
	 * accessor method for at handle
	 *
	 * @return string value of at handle
	 **/
	public function getAtHandle() {
		return($this->atHandle);
	}

	/**
	 * mutator method for at handle
	 *
	 * @param string $newAtHandle new value of at handle
	 * @throws \InvalidArgumentException if $newAtHandle is not a string or insecure
	 * @throws \RangeException if $newAtHandle is > 32 characters
	 * @throws \TypeError if $newAtHandle is not a string
	 **/
	public function setAtHandle(string $newAtHandle) {
		// verify the at handle is secure
		$newAtHandle = trim($newAtHandle);
		$newAtHandle = filter_var($newAtHandle, FILTER_SANITIZE_STRING);
		if(empty($newAtHandle) === true) {
			throw(new \InvalidArgumentException("at handle is empty or insecure"));
		}

		// verify the at handle will fit in the database
		if(strlen($newAtHandle) > 32) {
			throw(new \RangeException("at handle is too large"));
		}

		// store the at handle
		$this->atHandle = $newAtHandle;
	}

	/**
	 * accessor method for email
	 *
	 * @return string value of email
	 **/
	public function getEmail() {
		return $this->email;
	}

	/**
	 * mutator method for email
	 *
	 * @param string $newEmail new value of email
	 * @throws \InvalidArgumentException if $newEmail is not a valid email or insecure
	 * @throws \RangeException if $newEmail is > 128 characters
	 * @throws \TypeError if $newEmail is not a string
	 **/
	public function setEmail(string $newEmail) {
		// verify the email is secure
		$newEmail = trim($newEmail);
		$newEmail = filter_var($newEmail, FILTER_VALIDATE_EMAIL);
		if(empty($newEmail) === true) {
			throw(new \InvalidArgumentException("email is empty or insecure"));
		}

		// verify the email will fit in the database
		if(strlen($newEmail) > 128) {
			throw(new \RangeException("email is too large"));
		}

		// store the email
		$this->email = $newEmail;
	}

	/**
	 * accessor method for phone
	 *
	 * @return string value of phone
	 **/
	public function getPhone() {
		return($this->phone);
	}

	/**
	 * mutator method for phone
	 *
	 * @param string $newPhone new value of phone
	 * @throws \InvalidArgumentException if $newPhone is not a string or insecure
	 * @throws \RangeException if $newPhone is > 32 characters
	 * @throws \TypeError if $newPhone is not a string
	 **/
	public function setPhone(string $newPhone) {
		// verify the phone is secure
		$newPhone = trim($newPhone);
		$newPhone = filter_var($newPhone, FILTER_SANITIZE_STRING);
		if(empty($newPhone) === true) {
			throw(new \InvalidArgumentException("phone is empty or insecure"));
		}

		// verify the phone will fit in the database
		if(strlen($newPhone) > 32) {
			throw(new \RangeException("phone is too large"));
		}

		// store the phone
		$this->phone = $newPhone;
	}

	/**
	 * inserts this Profile into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) {
		// enforce the profileId is null (i.e., don't insert a profile that already exists)
		if($this->profileId !== null) {
			throw(new \PDOException("not a new profile"));
		}

		// create query template
		$query = "INSERT INTO profile(email, phone, atHandle) VALUES(:email, :phone, :atHandle)";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = ["email" => $this->email, "phone" => $this->phone, "atHandle" => $this->atHandle];
		$statement->execute($parameters);

		// update the null profileId with what mySQL just gave us
		$this->profileId = intval($pdo->lastInsertId());
	}

	/**
	 * deletes this Profile from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) {
		// enforce the profileId is not null (i.e., don't delete a profile that does not exist)
		if($this->profileId === null) {
			throw(new \PDOException("unable to delete a profile that does not exist"));
		}

		// create query template
		$query = "DELETE FROM profile WHERE profileId = :profileId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = ["profileId" => $this->profileId];
		$statement->execute($parameters);
	}

	/**
	 * updates this Profile from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) {
		// enforce the profileId is not null (i.e., don't update a profile that does not exist)
		if($this->profileId === null) {
			throw(new \PDOException("unable to delete a profile that does not exist"));
		}

		// create query template
		$query = "UPDATE profile SET email = :email, phone = :phone, atHandle = :atHandle WHERE profileId = :profileId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = ["email" => $this->email, "phone" => $this->phone, "atHandle" => $this->atHandle, "profileId" => $this->profileId];
		$statement->execute($parameters);
	}

	/**
	 * gets the Profile by profile id
	 *
	 * @param \PDO $pdo $pdo PDO connection object
	 * @param int $profileId profile id to search for
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByProfileId(\PDO $pdo, int $profileId) {
		// sanitize the profile id before searching
		if($profileId <= 0) {
			throw(new \PDOException("profile id is not postive"));
		}

		// create query template
		$query = "SELECT profileId, email, phone, atHandle FROM profile WHERE profileId = :profileId";
		$statement = $pdo->prepare($query);

		// bind the profile id to the place holder in the template
		$parameters = ["profileId" => $profileId];
		$statement->execute($parameters);

		// grab the Profile from mySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$profile = new Profile($row["profileId"], $row["atHandle"], $row["email"], $row["phone"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($profile);
	}

	/**
	 * gets the Profile by email
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $email email to search for
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByEmail(\PDO $pdo, string $email) {
		// sanitize the email before searching
		$email = trim($email);
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if(empty($email) === true) {
			throw(new \PDOException("not a valid email"));
		}

		// create query template
		$query = "SELECT profileId, email, phone, atHandle FROM profile WHERE email = :email";
		$statement = $pdo->prepare($query);

		// bind the profile id to the place holder in the template
		$parameters = ["email" => $email];
		$statement->execute($parameters);

		// grab the Profile from mySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$profile = new Profile($row["profileId"], $row["atHandle"], $row["email"], $row["phone"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($profile);
	}

	/**
	 * gets the Profile by at handle
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $atHandle at handle to search for
	 * @return Profile|null Profile or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByAtHandle(\PDO $pdo, string $atHandle) {
		// sanitize the at handle before searching
		$atHandle = trim($atHandle);
		$atHandle = filter_var($atHandle, FILTER_SANITIZE_STRING);
		if(empty($atHandle) === true) {
			throw(new \PDOException("not a valid at handle"));
		}

		// create query template
		$query = "SELECT profileId, email, phone, atHandle FROM profile WHERE atHandle = :atHandle";
		$statement = $pdo->prepare($query);

		// bind the profile id to the place holder in the template
		$parameters = ["atHandle" => $atHandle];
		$statement->execute($parameters);

		// grab the Profile from mySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$profile = new Profile($row["profileId"], $row["atHandle"], $row["email"], $row["phone"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($profile);
	}

	/**
	 * formats the state variables for JSON serialization
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() {
		return(get_object_vars($this));
	}
}