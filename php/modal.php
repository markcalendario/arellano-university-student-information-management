<?php
    function showCreateNewSectionModal($sectionName, $sectionStrand, $sectionAdviser) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-exclamation-circle"></i>
                        <h2>Do you really want to add new section?</h2>
                        <form method="post">
                            <div class="create-section-information">
                                <input type="hidden" name="create-section-name" value="<?php echo $sectionName; ?>">
                                <input type="hidden" name="create-section-strand" value="<?php echo $sectionStrand; ?>">
                                <input type="hidden" name="create-section-adviser" value="<?php echo $sectionAdviser; ?>">
                            </div>
                            <div class="button-container">
                                <button name="create-section" class="primary-button" type="submit"> <i class="fad fa-thumbs-up"></i> Create Section </button>
                                <button type="submit" name="cancel-create-section" class="cancel-action-modal"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showWelcomerModal($mainString, $secondaryString) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-file-certificate" style="color: darkorange"></i>
                        <h2><?php echo $mainString; ?></h2>
                        <br>
                        <p><?php echo $secondaryString; ?></p>
                        <form method="post">
                            <div id="welcomerModalForm" class="button-container">
                                <button id="iAgreePrepare" type="button" name="close" class="primary-button cancel-action-modal"> Read First</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showSectionInformationEditorModal($sectionID, $sectionName, $strandID, $adviserID, $sectionStatus) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <h2>EDIT SECTION</h2>
                        <form method="post">
                            <div class="inputs-container">
                                <input type="text" name="edit-section-id" value="<?php echo $sectionID; ?>" hidden>
                                <input type="text" name="edit-section-name" placeholder="Section Name" value="<?php echo $sectionName; ?>">
                                <select name="edit-section-strand" id="">
                                    <?php getCurrentStrandSelectOption($strandID); ?>
                                    <?php populateStrandSelectOption(); ?>
                                </select>
                                <select name="edit-section-adviser" id="">
                                    <?php getCurrentSectionAdviserSelectOption($adviserID); ?>
                                    <?php populateAddSectionAdvisersOption(); ?>
                                </select>
                                <select name="edit-section-status" id="">
                                    <option value="<?php echo encryptData('1'); ?>" <?php if ($sectionStatus == 1) { echo "selected='selected'"; }  ?>> ACTIVE </option>
                                    <option value="<?php echo encryptData('0'); ?>" <?php if ($sectionStatus == 0) { echo "selected='selected'"; }  ?>> INACTIVE </option>
                                </select>
                            </div>
                            <div class="button-container">
                                <button name="edit-section" class="primary-button" type="submit"> <i class="fad fa-pen"></i> Edit Section </button>
                                <button type="submit" name="cancel-edit-section" class="cancel-action-modal"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showPostAnnouncementModal() {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <h2>POST ANNOUNCEMENT</h2>
                        <form method="post">
                            <div class="inputs-container">
                                <input type="text" name="post-announcement-title" placeholder="Announcement Title">
                                <input type="text" name="post-announcement-content" placeholder="Announcement Content">
                            </div>
                            <div class="button-container">
                                <button name="post-announcement" class="primary-button" type="submit"> <i class="fad fa-bullhorn"></i> Announce </button>
                                <button type="submit" name="cancel-announce" class="cancel-action-modal"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showDeleteAnnouncementModal($announcementToDelete) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-trash"></i>
                        <h2>Do you really want to delete this announcement?</h2>
                        <form method="post">
                            <div class="button-container">
                                <input type="hidden" name="announcement-to-delete" value="<?php echo $announcementToDelete; ?>">
                                <button name="delete-announcement-final" class="danger-button" type="submit"> <i class="fad fa-trash"></i> Delete Announcement </button>
                                <button type="submit" name="cancel-delete-announcement" class="cancel-action-modal"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showEditStudentModal() {
        $viewStudent = new viewStudent;
        ?>
        <div class="modal-overlay">
            <section id="modal" class="edit-student-modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <h2>Edit Student Information</h2>
                        <form method="post">
                            <div class="edit-field-container">
                                <input type="hidden" name="edit-student-id" value="<?php echo encryptData($viewStudent->getStudentUserID()); ?>">
                                <label>First Name</label>
                                <input type="text" name="edit-student-firstname" value="<?php echo $viewStudent->getStudentFirstName(); ?>">
                                <label>Middle Name</label>
                                <input type="text" name="edit-student-middlename" value="<?php echo $viewStudent->getStudentMiddleName(); ?>">
                                <label>Last Name</label>
                                <input type="text" name="edit-student-lastname" value="<?php echo $viewStudent->getStudentlastName(); ?>">
                                <label>Gender</label>
                                <select name="edit-student-gender">
                                    <option value="Female" <?php if ($viewStudent->getStudentGender() == 'Female') { echo "selected='selected'"; } ?>>Female</option>
                                    <option value="Male" <?php if ($viewStudent->getStudentGender() == 'Male') { echo "selected='selected'"; } ?>>Male</option>
                                </select>
                                <label>Birthday</label>
                                <input type="date" name="edit-student-birthday" value="<?php echo $viewStudent->getStudentRawBirthday(); ?>">
                                <label>Religion</label>
                                <select name="edit-student-religion">
                                    <option value="" <?php if (isset($_POST['religion']) && $_POST['religion'] == "") { echo "selected=\"selected\""; } ?>>Select religion</option>
                                    <option value="Catholic" <?php if ($viewStudent->getStudentReligion() == "Catholic") { echo "selected=\"selected\""; } ?>>Catholic</option>
                                    <option value="Iglesia Ni Cristo" <?php if ($viewStudent->getStudentReligion() == "Iglesia Ni Cristo") { echo "selected=\"selected\""; } ?>>Iglesia Ni Cristo</option>
                                    <option value="Born Again" <?php if ($viewStudent->getStudentReligion() == "Born Again") { echo "selected=\"selected\""; } ?>>Born Again</option>
                                    <option value="Members Church of God International" <?php if ($viewStudent->getStudentReligion() == "Members Church of God International") { echo "selected=\"selected\""; } ?>>Members of Church of God International</option>
                                    <option value="Church of Christ 4th Watch" <?php if ($viewStudent->getStudentReligion() == "Church of Christ 4th Watch") { echo "selected=\"selected\""; } ?>>Church of Christ 4th Watch</option>
                                    <option value="Mormon" <?php if ($viewStudent->getStudentReligion() == "Mormon") { echo "selected=\"selected\""; } ?>>Mormon</option>
                                    <option value="Jehovah's Witness" <?php if ($viewStudent->getStudentReligion() == "Jehovah's Witness") { echo "selected=\"selected\""; } ?>>Jehovah's Witness</option>
                                    <option value="Other" <?php if ($viewStudent->getStudentReligion() == "Other") { echo "selected=\"selected\""; } ?>>Other</option>
                                </select>
                                <label>Country</label>
                                <input type="text" name="edit-student-country" value="<?php echo $viewStudent->getStudentCountry(); ?>">
                                <label>Region</label>
                                <input type="text" name="edit-student-region" value="<?php echo $viewStudent->getStudentRegion(); ?>">
                                <label>Complete Address</label>
                                <input type="text" name="edit-student-address" value="<?php echo $viewStudent->getStudentAddress(); ?>">
                                <label>Contact</label>
                                <input type="text" name="edit-student-contact" value="<?php echo $viewStudent->getStudentContact(); ?>">

                                <h2>Family Background</h2>

                                <label>Mother Name</label>
                                <input type="text" name="edit-student-mother-name" value="<?php echo $viewStudent->getStudentMotherName(); ?>">
                                <label>Mother Work</label>
                                <input type="text" name="edit-student-mother-work" value="<?php echo $viewStudent->getStudentMotherWork(); ?>">
                                <label>Mother Contact</label>
                                <input type="text" name="edit-student-mother-contact" value="<?php echo $viewStudent->getStudentMotherContact(); ?>">

                                <label>Father Name</label>
                                <input type="text" name="edit-student-father-name" value="<?php echo $viewStudent->getStudentFatherName(); ?>">
                                <label>Father Work</label>
                                <input type="text" name="edit-student-father-work" value="<?php echo $viewStudent->getStudentFatherWork(); ?>">
                                <label>Father Contact</label>
                                <input type="text" name="edit-student-father-contact" value="<?php echo $viewStudent->getStudentFatherContact(); ?>">

                                <label>Guardian Name</label>
                                <input type="text" name="edit-student-guardian-name" value="<?php echo $viewStudent->getStudentGuardianName(); ?>">
                                <label>Guardian Contact</label>
                                <input type="text" name="edit-student-guardian-contact" value="<?php echo $viewStudent->getStudentGuardianContact(); ?>">
                                <label>Relationship to Guardian</label>
                                <input type="text" name="edit-student-relationship" value="<?php echo $viewStudent->getStudentRelationshipWithGuardian(); ?>">

                                <h2>School's File</h2>

                                <label>Learner's Reference Number</label>
                                <input type="text" name="edit-student-lrn" value="<?php echo $viewStudent->getStudentLRN(); ?>"> 
                                <label>Section</label>
                                <select name="edit-student-section">
                                    <?php 
                                        populateCurrentSectionSelection();
                                        populateSectionSelection(); 
                                    ?>
                                </select>
                                
                            </div>
                            <div class="button-container">
                                <button name="update-student-info" class="primary-button" type="submit"> <i class="fad fa-check"></i> Update </button>
                                <button type="submit" name="cancel-update-student" class="cancel-action-modal"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    
    function showTeacherDeletePromptModal($teacherFirstname, $teacherLastname, $teacherID) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-trash"></i>
                        <h2>Are you sure?</h2>
                        <p>By doing this, the account of <?php echo decryptData($teacherFirstname) . " " . decryptData($teacherLastname); ?> will be permanently deleted.</p>
                        <form method="post">
                            <input type="hidden" name="teacher-to-delete-firstname" value="<?php echo $teacherFirstname; ?>">
                            <input type="hidden" name="teacher-to-delete-lastname" value="<?php echo $teacherLastname; ?>">
                            <input type="hidden" name="teacher-to-delete-id" value="<?php echo $teacherID; ?>">
                            <div class="button-container">
                                <button name="delete-teacher-permanently" class="" type="submit"> <i class="fad fa-trash"></i> Delete Teacher </button>
                                <button type="submit" name="cancel-delete" class="primary-button"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showDeleteStudentModal($studentID, $studentHashedFullName) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-trash"></i>
                        <h2>Are you sure?</h2>
                        <p>By doing this, the account of <?php echo decryptData($studentHashedFullName); ?> will be permanently deleted.</p>
                        <form method="post">
                            <input type="hidden" name="student-to-delete-fullname" value="<?php echo $studentHashedFullName; ?>">
                            <input type="hidden" name="student-to-delete-id" value="<?php echo $studentID; ?>">
                            <div class="button-container">
                                <button name="delete-student-permanently" class="" type="submit"> <i class="fad fa-trash"></i> Delete Student </button>
                                <button type="submit" name="cancel-delete" class="primary-button"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showCreateGradeFieldModal() {
        $viewStudentGrades = new viewStudentGrades;
         ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-book"></i>
                        <h2>Create Grading Field</h2>
                        <br>
                        <p> <?php echo 'Create grade for ' . $viewStudentGrades->fullName; ?> </p>
                        <form method="post">
                            <p> <i class="fas fa-info-circle"></i> This lists the all of the available subject in your strand. If there are no options, you have already graded this student to all available subjects.</p>
                            <select name="subject-grade-field">
                                <option value="">Select Subjects</option>
                                <?php fetchSubjects(); ?>
                            </select>
                            <div class="button-container">
                                <button name="create-grade-field-final" class="primary-button" type="submit"> <i class="fad fa-check"></i> Create </button>
                                <button type="submit" name="cancel-create-grade-field"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showAddSubjectModal() {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-book"></i>
                        <h2>Create New Subject</h2>
                        <form method="post">
                            <div class="inputs-container">
                                <input type="text" placeholder="Subject Name" name="create-subject-name">
                                <select name="create-subject-type">
                                    <option value="">Select Subject Type</option>
                                    <option value="<?php echo encryptData(0); ?>">Core Subject</option>
                                    <option value=" <?php echo encryptData(1); ?> ">Specialization Subject</option>
                                </select>
                                <select name="create-subject-semester">
                                    <option value="">Select Semester</option>
                                    <option value="<?php echo encryptData(1); ?>">First Semester</option>
                                    <option value=" <?php echo encryptData(2); ?> ">Second Semester</option>
                                </select>
                            </div>
                            <div class="button-container">
                                <button name="create-subject-final" class="primary-button" type="submit"> <i class="fad fa-book"></i> Create Subject </button>
                                <button type="submit" name="cancel"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showDeleteSubjectModal($subjectID, $subjectName) {
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-trash"></i>
                        <h2>Are you sure?</h2>
                        <p>By doing this, the subject <?php echo decryptData($subjectName); ?> will be permanently deleted.</p>
                        <form method="post">
                            <input type="hidden" name="subject-to-delete-id" value="<?php echo $subjectID; ?>">
                            <input type="hidden" name="subject-to-delete-name" value="<?php echo $subjectName; ?>">
                            <div class="button-container">
                                <button name="delete-subject-permanently" class="" type="submit"> <i class="fad fa-trash"></i> Delete Subject </button>
                                <button type="submit" name="cancel" class="primary-button"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }

    function showStrandEditorModal() {
        $strand = new strand;
        ?>
        <div class="modal-overlay">
            <section id="modal">
                <div class="container-fluid">
                    <div class="wrapper">
                        <i class="modal-icon fad fa-edit"></i>
                        <h2>Strand Editor</h2>
                        <p><?php echo $strand->strandGrade . ' ' . $strand->strandName; ?></p>
                        <form method="post">
                            <div class="inputs-container">
                                <input type="text" name="new-strand-name" value="<?php echo $strand->strandName; ?>">
                                <select name="new-grade-level">
                                    <option value="<?php echo encryptData(11); ?>" <?php if ($strand->strandGradeRaw == 11) { echo "selected"; } ?>>Grade 11</option>
                                    <option value="<?php echo encryptData(12); ?>" <?php if ($strand->strandGradeRaw == 12) { echo "selected"; } ?>>Grade 12</option>
                                </select>
                            </div>
                            <div class="button-container">
                                <button name="save-strand" class="primary-button" type="submit"> <i class="fad fa-save"></i> Save </button>
                                <button type="submit" name="cancel"> <i class="fad fa-times-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
    

?>