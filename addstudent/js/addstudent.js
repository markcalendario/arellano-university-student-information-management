// Step Progress

const informationPage = document.getElementsByClassName('information-page');
const progressBar = document.querySelector('.step-progress');
const stepPercent = document.querySelector('.step-percent');

const next = document.getElementsByClassName('next-button');
const prev = document.getElementsByClassName('prev-button');

const indicator = document.getElementsByClassName('indicator');

var currentPosition = 0;

function setForm() {
    for (let i = 0; i < informationPage.length; i++) {
        informationPage[i].style.display = "none";
        if (i == currentPosition) {
            informationPage[currentPosition].style.display = 'block';
        }
        
    }
    window.scroll(0, 0);

}

for (let i = 0; i < next.length; i++) {
    next[i].addEventListener('click', function() {
        currentPosition++;
        setForm();
        setIndicator();
    });
}

for (let i = 0; i < prev.length; i++) {
    prev[i].addEventListener('click', function() {
        currentPosition--;
        setForm();
        setIndicator();
    });
    
}

function setIndicator() {
    for (let i = 0; i < indicator.length; i++) {
        indicator[i].style.color = "black";

        if (i == currentPosition) {
            indicator[i].style.color = "royalblue";
        }
        
    }
}

function initForm() {
    setForm();
    setIndicator();
}

initForm();

// ============================
// Automatic age calculator
// ============================

var age = document.getElementById("age");
var birthday = document.getElementById("birthday");
birthday.onchange = function () {
    setAge(birthday.value);
}

function setAge(birthday) {
    var today = new Date();
    var birthdate = new Date(birthday);
    var birthYear = birthdate.getFullYear();
    var birthDay = birthdate.getDate();
    var birthMonth = birthdate.getMonth(); 

    if (isBirthYearValid(birthYear)) {
        if (isBeforeBirthday(birthMonth, birthDay)) {
            age.value = (today.getFullYear() - birthYear) - 1;
        } else {
            age.value = (today.getFullYear() - birthYear);
        }
    } 
}

function isBeforeBirthday(birthMonth, birthDay) {
    var today = new Date();
    
    if (today.getMonth() < birthMonth) {
        return 1;
    } else if (today.getMonth() == birthMonth && today.getDate() < birthDay) {
        return 1;
    } else {
        return 0;
    }
}

function isBirthYearValid(year) {
    if (year > 1900) {
        return 1;
    }
}

//
// Modal Close
//

const modal = document.querySelector('.modal-overlay');
const closeBtn = document.querySelector('.cancel-action-modal');

if (closeBtn != null) {
    closeBtn.addEventListener('click', function () {
        modal.remove();
    });
    
}