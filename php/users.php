<?php 

define('NOTSET', -1);
define('STUDENT', 0);
define('TEACHER', 1);
define('FACULTY', 2);

class user {

    # CREDENTIALS
    private $userID = '';
    private $userType = NOTSET;
    private $userTypeText = '';
    private $accountStatus = '';
    private $profilePicStatus = '';
    
    # USERS BASIC INFORMATION
    private $firstName = '';
    private $middleName = '';
    private $lastName = '';
    private $gender = '';
    private $birthday = '';
    private $religion = '';
    private $country = '';
    private $region = '';
    private $address = '';
    private $contact = '';
    private $age = '';

    # USERS FAMILY BACKGROUND
    private $motherFullName = '';
    private $motherWork = '';
    private $motherContact = '';
    private $fatherFullName = '';
    private $fatherWork = '';
    private $fatherContact = '';
    private $guardianName = '';
    private $guardianContact = '';
    private $relationship = '';


    public function __construct() {

        $con = connect();

        $sql = "SELECT * FROM user_information 
                INNER JOIN user_family_background ON user_information.id = user_family_background.id 
                INNER JOIN user_credentials ON user_information.id = user_credentials.id 
                WHERE user_information.id = ?";

        $stmt = $con -> prepare($sql);
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($data = $result->fetch_assoc()) {

            # BASIC INFORMATION
            $this->setUserCredentials($data);
            # BASIC INFORMATION
            $this->setBasicInformation($data);
            # FAMILY BACKGROUND
            $this->setFamilyBackground($data);

        }
    }

    # ===== INFO CONSTRUCTORS ===== #

    private function setUserCredentials($data) {
        $this->setUserID($data['id']);
        $this->setUserType($data['usertype']);
        $this->setAccountStatus($data['account_status']);
        $this->setProfilePicStatus($data['profile_status']);
    }

    private function setFamilyBackground($data) {
        $this->setMotherName($data['mother_fullname']);
        $this->setMotherWork($data['mother_work']);
        $this->setMotherContact($data['mother_contact']);
        $this->setFatherName($data['father_fullname']);
        $this->setFatherWork($data['father_work']);
        $this->setFatherContact($data['father_contact']);
        $this->setGuardianName($data['guardian_fullname']);
        $this->setGuardianContact($data['guardian_contact']);
        $this->setRelationshipWithGuardian($data['relationship']);
    }

    private function setBasicInformation($data) {
        $this->setFirstName($data['firstname']);
        $this->setMiddleName($data['middlename']);
        $this->setlastname($data['lastname']);
        $this->setGender($data['gender']);
        $this->setBirthday($data['birthday']);
        $this->setReligion($data['religion']);
        $this->setCountry($data['country']);
        $this->setRegion($data['region']);
        $this->setAddress($data['address']);
        $this->setContact($data['contact']);
        $this->setAge($data['birthday']);
    }

    # ===== USER TYPES ====== #
    # Note : USER TYPES
    #        0 = STUDENT
    #        1 = TEACHER
    #        2 = FACULTY
    
    public function isStudent() {
        $isStudent = $this->getUserType() == STUDENT && $this->getUserType() != NOTSET ? 1 : 0;
        return $isStudent;
    }

    public function isTeacher() {
        $isTeacher = $this->getUserType() == TEACHER && $this->getUserType() != NOTSET ? 1 : 0;
        return $isTeacher;
    }

    public function isFaculty() {
        $isFaculty = $this->getUserType() == FACULTY && $this->getUserType() != NOTSET ? 1 : 0;
        return $isFaculty;
    }

    public function getUserTypeOnText() {
        if ($this->isFaculty()) {
            return 'Faculty Admin';
        } else if ($this->isTeacher()) {
            return 'Adviser';
        } else {
            return 'Student';
        }
    }

    # ===== USER CREDENTIALS ===== #

    private function setProfilePicStatus($profilePicStatus) {
        $this->profilePicStatus = $profilePicStatus;
    }

    public function getProfilePicStatus() {
        return $this->profilePicStatus;
    }

    private function setAccountStatus($accountStatus) {
        $this->accountStatus = $accountStatus;
    }
    
    public function getAccountStatus() {
        return $this->accountStatus;
    }

    public function isAccountActive() {
        return $this->accountStatus == 1;
    }

    private function setUserType($userType) {
        $this->userType = $userType;
    }
    
    public function getUserType() {
        return $this->userType;
    }

    private function setUserID($userID) {
        $this->userID = $userID;
    }
    
    public function getUserID() {
        return $this->userID;
    }

    # ===== FAMILY INFORMATION ===== #

    private function setMotherName($motherName) {
        $this->motherFullName = $motherName;
    }
    
    public function getMotherName() {
        return $this->motherFullName;
    }

    private function setMotherWork($motherWork) {
        $this->motherWork = $motherWork;
    }
    
    public function getMotherWork() {
        return $this->motherWork;
    }

    private function setMotherContact($motherContact) {
        $this->motherContact = $motherContact;
    }
    
    public function getMotherContact() {
        return $this->motherContact;
    }

    private function setFatherName($fatherFullName) {
        $this->fatherFullName = $fatherFullName;
    }
    
    public function getFatherName() {
        return $this->fatherFullName;
    }

    private function setFatherWork($fatherWork) {
        $this->fatherWork = $fatherWork;
    }
    
    public function getFatherWork() {
        return $this->fatherWork;
    }

    private function setFatherContact($fatherContact) {
        $this->fatherContact = $fatherContact;
    }
    
    public function getFatherContact() {
        return $this->fatherContact;
    }

    private function setGuardianName($guardianName) {
        $this->guardianName = $guardianName;
    }
    
    public function getGuardianName() {
        return $this->guardianName;
    }

    private function setGuardianContact($guardianContact) {
        $this->guardianContact = $guardianContact;
    }
    
    public function getGuardianContact() {
        return $this->guardianContact;
    }

    private function setRelationshipWithGuardian($relationship) {
        $this->relationship = $relationship;
    }
    
    public function getRelationshipWithGuardian() {
        return $this->relationship;
    }

    # ===== BASIC INFORMATION ===== #

    public function getFullName() {
        if ($this->getMiddleName() == '') {
            return $this->getFirstName(). " ". $this->getlastName();
        } else {
            return $this->getFirstName() . " " . " " . substr($this->getMiddleName(), 0,1) . ". " .$this->getlastName();
        }
    }
    
    private function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    public function getFirstName() {
        return $this->firstName;
    }

    private function setMiddleName($middleName) {
        $this->middleName = $middleName;
    }
    
    public function getMiddleName() {
        return $this->middleName;
    }

    private function setlastName($lastName) {
        $this->lastName = $lastName;
    }
    
    public function getlastName() {
        return $this->lastName;
    }

    private function setGender($gender) {
        $this->gender = $gender;
    }
    
    public function getGender() {
        return $this->gender;
    }

    private function setBirthday($birthday) {
        $this->birthday = $birthday;
    }
    
    public function getBirthday() {
        return $this->birthday;
    }

    public function getSentenceTypeBirthday() {
        return date('F m, Y', strtotime($this->birthday));
    }

    private function setReligion($religion) {
        $this->religion = $religion;
    }
    
    public function getReligion() {
        return $this->religion;
    }

    private function setCountry($country) {
        $this->country = $country;
    }
    
    public function getCountry() {
        return $this->country;
    }

    private function setRegion($region) {
        $this->region = $region;
    }
    
    public function getRegion() {
        return $this->region;
    }

    private function setAddress($address) {
        $this->address = $address;
    }
    
    public function getAddress() {
        return $this->address;
    }

    private function setContact($contact) {
        $this->contact = $contact;
    }
    
    public function getContact() {
        return $this->contact;
    }

    private function setAge($birthday) {
        $birthday = new DateTime($birthday);
        $now = new DateTime();
        $age = $now->diff($birthday);
        $this->age = $age->y;
    }

    public function getAge() {
        return $this->age;
    }

}

class teacher {

    # IF TEACHER

    private $sectionID;
    private $sectionName;
    private $strandName;
    private $gradeLevel; 
    private $sectionStatus;
    private $sectionStrandID;

    public function __construct() {
        $con = connect();
        $stmt = $con->prepare(
            'SELECT
            sections.id, 
            section_name, 
            sections.strand_id,
            section_status,
            strand_name, 
            strand_grade 
            FROM sections
            INNER JOIN strands
            ON strands.strand_id = sections.strand_id
            WHERE adviser_id = ?'
        );
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($sectionID, $sectionName, $strandId, $sectionStatus, $strandName, $gradeLevel);

        while ($stmt->fetch()) {
            $this->sectionID = $sectionID;
            $this->sectionName = $sectionName;
            $this->sectionStrandID = $strandId;
            $this->strandName = $strandName;
            $this->gradeLevel = $gradeLevel;
            $this->sectionStatus = $sectionStatus; 
        }
    }

    public function isHandlingASection() {
        $isHandlingASection = $this->sectionID != null ? true : false;
        return $isHandlingASection;
    }

    public function getSectionAndGradeLevel() {
        return $this->gradeLevel . " - " . $this->sectionName;
    }

    public function getStrandName() {
        return $this->strandName;
    }

    public function getSectionStrandID() {
        return $this->sectionStrandID;
    }

    public function getSectionGradeLevel() {
        return $this->gradeLevel;
    }

    public function getHandleSectionID() {
        return $this->sectionID;
    }

    public function isSectionActive() {
        return $this->sectionStatus == 1;
    }
}

class student {
    public $studentSectionID;
    public $studentSectionName;
    public $studentStrandID;
    public $studentStrandName;
    public $studentStrandGrade;
    public $studentFullSectionDescription;
    public $studentFullSectionWithStrandDescription;
    public $lrn;

    public function __construct() {
        $con = connect();

        $stmt = $con->prepare('SELECT 
        lrn, user_school_info.section,  
        section_name, strand_name,
        strand_grade, strands.strand_id,
        CONCAT(strand_grade, " - ", section_name),
        CONCAT("Grade ", strand_grade, " - ", section_name, " ", strand_name) 
        FROM user_school_info 
        LEFT JOIN sections 
        ON user_school_info.section = sections.id
        LEFT JOIN strands 
        ON strands.strand_id = sections.strand_id
        WHERE user_school_info.id = ?');
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $lrn, 
            $studentSectionID, 
            $studentSectionName, 
            $studentStrandName,
            $studentStrandGrade,
            $studentStrandID,
            $studentFullSectionDescription,
            $studentFullSectionWithStrandDescription
        );
        $stmt->fetch();

        $this->lrn = $lrn;
        $this->studentSectionID = $studentSectionID;
        $this->studentSectionName = $studentSectionName;
        $this->studentStrandID = $studentStrandID;
        $this->studentStrandName = $studentStrandName;
        $this->studentStrandGrade = $studentStrandGrade;
        $this->studentFullSectionDescription = $studentFullSectionDescription;
        $this->studentFullSectionWithStrandDescription = $studentFullSectionWithStrandDescription;

    }
}

?>