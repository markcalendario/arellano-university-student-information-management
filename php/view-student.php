<?php 

class viewStudent {

    # CREDENTIALS
    private $studentUserID = '';
    private $userType = NOTSET;
    
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

    # SCHOOL INFO
    private $lrn = '';
    private $studentSectionName = '';
    private $studentGrade = '';
    private $strandName = '';

    public function __construct() {

        $con = connect();

        $viewStudentID = decryptData($_SESSION['viewStudent']);

        $sql = "SELECT * FROM user_information 
                INNER JOIN user_family_background ON user_information.id = user_family_background.id 
                INNER JOIN user_credentials ON user_information.id = user_credentials.id
                INNER JOIN user_school_info ON user_information.id = user_school_info.id 
                WHERE user_information.id = ?";

        $stmt = $con -> prepare($sql);
        $stmt->bind_param('i', $viewStudentID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($data = $result->fetch_assoc()) {

            # CREDENTIALS
            $this->setStudentUserCredentials($data);
            # BASIC INFORMATION
            $this->setStudentBasicInformation($data);
            # FAMILY BACKGROUND
            $this->setStudentFamilyBackground($data);
            # SCHOOL INFORMATION
            $this->setStudentSchoolInformation($data);

        }
    }

    # ===== INFO CONSTRUCTORS ===== #

    private function setStudentSchoolInformation($data) {
        $con = connect();
        $sectionID = $data['section'];
        $stmt = $con->prepare(
            'SELECT section_name, strand_grade, strand_name 
            FROM sections
            INNER JOIN strands ON sections.strand_id = strands.strand_id
            WHERE sections.id = ?'
        );

        $stmt->bind_param('i', $sectionID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($sectionName, $studentGrade, $strandName);
        $stmt->fetch();

        $this->setStudentLRN($data['lrn']);
        $this->setStudentSectionName($sectionName);
        $this->setStudentStrand($strandName); 
        $this->setStudentGrade($studentGrade);
    }

    private function setStudentUserCredentials($data) {
        $this->setStudentUserID($data['id']);
        $this->setStudentUserType($data['usertype']);
    }

    private function setStudentFamilyBackground($data) {
        $this->setStudentMotherName($data['mother_fullname']);
        $this->setStudentMotherWork($data['mother_work']);
        $this->setStudentMotherContact($data['mother_contact']);
        $this->setStudentFatherName($data['father_fullname']);
        $this->setStudentFatherWork($data['father_work']);
        $this->setStudentFatherContact($data['father_contact']);
        $this->setStudentGuardianName($data['guardian_fullname']);
        $this->setStudentGuardianContact($data['guardian_contact']);
        $this->setStudentRelationshipWithGuardian($data['relationship']);
    }

    private function setStudentBasicInformation($data) {
        $this->setStudentFirstName($data['firstname']);
        $this->setStudentMiddleName($data['middlename']);
        $this->setStudentlastname($data['lastname']);
        $this->setStudentGender($data['gender']);
        $this->setStudentBirthday($data['birthday']);
        $this->setStudentReligion($data['religion']);
        $this->setStudentCountry($data['country']);
        $this->setStudentRegion($data['region']);
        $this->setStudentAddress($data['address']);
        $this->setStudentContact($data['contact']);
    }

    # ===== USER SCHOOL CREDENTIALS ===== #
    public function getStudentLRN() {
        return $this->lrn;
    }

    private function setStudentLRN($lrn) {
        $this->lrn = $lrn;
    }

    public function getStudentStrand() {
        return $this->strandName;
    }

    private function setStudentStrand($strandName) {
        $this->strandName = $strandName;
    }
    public function getStudentGrade() {
        return $this->studentGrade;
    }

    private function setStudentGrade($studentGrade) {
        $this->studentGrade = $studentGrade;
    }
    
    public function getStudentSectionName() {
        return $this->studentSectionName;
    }

    private function setStudentSectionName($sectionName) {
        $this->studentSectionName = $sectionName;
    }

    # ===== USER CREDENTIALS ===== #

    private function setStudentUserType($userType) {
        $this->userType = $userType;
    }
    
    public function getStudentUserType() {
        return $this->userType;
    }

    private function setStudentUserID($studentUserID) {
        $this->studentUserID = $studentUserID;
    }
    
    public function getStudentUserID() {
        return $this->studentUserID;
    }

    # ===== FAMILY INFORMATION ===== #

    private function setStudentMotherName($motherFullName) {
        $this->motherFullName = $motherFullName;
    }
    
    public function getStudentMotherName() {
        return $this->motherFullName;
    }

    private function setStudentMotherWork($motherWork) {
        $this->motherWork = $motherWork;
    }
    
    public function getStudentMotherWork() {
        return $this->motherWork;
    }

    private function setStudentMotherContact($motherContact) {
        $this->motherContact = $motherContact;
    }
    
    public function getStudentMotherContact() {
        return $this->motherContact;
    }

    private function setStudentFatherName($fatherFullName) {
        $this->fatherFullName = $fatherFullName;
    }
    
    public function getStudentFatherName() {
        return $this->fatherFullName;
    }

    private function setStudentFatherWork($fatherWork) {
        $this->fatherWork = $fatherWork;
    }
    
    public function getStudentFatherWork() {
        return $this->fatherWork;
    }

    private function setStudentFatherContact($fatherContact) {
        $this->fatherContact = $fatherContact;
    }
    
    public function getStudentFatherContact() {
        return $this->fatherContact;
    }

    private function setStudentGuardianName($guardianName) {
        $this->guardianName = $guardianName;
    }
    
    public function getStudentGuardianName() {
        return $this->guardianName;
    }

    private function setStudentGuardianContact($guardianContact) {
        $this->guardianContact = $guardianContact;
    }
    
    public function getStudentGuardianContact() {
        return $this->guardianContact;
    }

    private function setStudentRelationshipWithGuardian($relationship) {
        $this->relationship = $relationship;
    }
    
    public function getStudentRelationshipWithGuardian() {
        return $this->relationship;
    }

    # ===== BASIC INFORMATION ===== #

    public function getStudentFullName() {
        if ($this->getStudentMiddleName() == '') {
            return $this->getStudentFirstName(). " ". $this->getStudentlastName();
        } else {
            return $this->getStudentFirstName() . " " . " " . substr($this->getStudentMiddleName(), 0,1) . ". " .$this->getStudentlastName();
        }
    }
    
    private function setStudentFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    public function getStudentFirstName() {
        return $this->firstName;
    }

    private function setStudentMiddleName($middleName) {
        $this->middleName = $middleName;
    }
    
    public function getStudentMiddleName() {
        return $this->middleName;
    }

    private function setStudentlastName($lastName) {
        $this->lastName = $lastName;
    }
    
    public function getStudentlastName() {
        return $this->lastName;
    }

    private function setStudentGender($gender) {
        $this->gender = $gender;
    }
    
    public function getStudentGender() {
        return $this->gender;
    }

    private function setStudentBirthday($birthday) {
        $this->birthday = $birthday;
    }
    
    public function getStudentBirthday() {
        $birthDay = new DateTime($this->birthday);
        $birthdayFormatted = $birthDay->format('M/d/Y');
        return $birthdayFormatted;
    }

    public function getStudentRawBirthday() {
        return $this->birthday;
    }

    private function setStudentReligion($religion) {
        $this->religion = $religion;
    }
    
    public function getStudentReligion() {
        return $this->religion;
    }

    private function setStudentCountry($country) {
        $this->country = $country;
    }
    
    public function getStudentCountry() {
        return $this->country;
    }

    private function setStudentRegion($region) {
        $this->region = $region;
    }
    
    public function getStudentRegion() {
        return $this->region;
    }

    private function setStudentAddress($address) {
        $this->address = $address;
    }
    
    public function getStudentAddress() {
        return $this->address;
    }

    private function setStudentContact($contact) {
        $this->contact = $contact;
    }
    
    public function getStudentContact() {
        return $this->contact;
    }

    public function getAge() {
        $birthDay = new DateTime($this->birthday);
        $now = new DateTime();
        $age = $now->diff($birthDay);
        return $age->y;
    }

}

?>