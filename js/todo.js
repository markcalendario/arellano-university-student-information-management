var time = document.querySelector('.day-time');
var dateTodo = document.querySelector('.day-date');
var week = document.querySelector('.week');

var greet = document.querySelector('.greet');
var greetQuote = document.querySelector('.greet-quote');

class todoDate {
    constructor() {
        let date = new Date();
        let month = this.getMonthName(date.getMonth());
        let day = this.getStandardDay(date.getDate());
        let year = date.getFullYear();

        dateTodo.textContent = month + " " + day + ", " + year;
    }

    getStandardDay(i) {
        return (i < 9) ? "0" + i : i;
    }

    getMonthName(i) {
        const months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ]

        return months[i];
    }
}

class todoClock {

    constructor() {
        var date = new Date();

        let hours = this.getStandardHour(date.getHours());
        let minutes = this.doubleDigit(date.getMinutes());
        let seconds = this.doubleDigit(date.getSeconds());
        let meridiem = this.getMeridiem(date.getHours());
        let day = this.getDayName(date.getDay());
        time.textContent = hours + ":" + minutes + ":" + seconds + " " + meridiem;
        week.textContent = day;
        
        this.setGreet(date.getHours());
    }

    setGreet(i) {
        if (i >= 0 && i < 12) {
            greet.textContent = "Good morning, ";
        } else if (i >= 12 & i < 18) {
            greet.textContent = "Good afternoon, ";
        } else {
            greet.textContent = "Good evening, ";
        }
    }

    getDayName(i) {
        const weekNames = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];

        return weekNames[i];
    }

    doubleDigit(i) {
        return (i <= 9) ? "0" + i : i;
    }
    
    getStandardHour(hour) {
        if (hour > 12) {
            return hour - 12;
        } else if (hour == 0){
            return 12;
        } else {
            return hour;
        }
    }
    
    getMeridiem(hour) {
        if (hour >= 12) {
            return "PM";
        } else {
            return "AM";
        }
    }
}

function setGreetQuotes() {
    const quotes = [
        "Glory be unto God!",
        "Failures are the best teacher.",
        "I can pass all the obstacles."
    ];

    greetQuote.textContent = quotes[Math.floor(Math.random() * quotes.length)];
}


window.onload = function () {
    let clock = new todoClock;
    let tododate = new todoDate;
    setGreetQuotes();
}

setInterval(() => {
    let clock = new todoClock;
    let tododate = new todoDate;
}, 1000);

