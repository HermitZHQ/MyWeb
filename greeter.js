var Student = (function () {
    function Student(fiestName, lastName) {
        this.firstName = fiestName;
        this.lastName = lastName;
    }
    Student.prototype.greeter = function () {
        return "Hello，您好" + this.firstName + " " + this.lastName;
    };
    return Student;
})();

var user = new Student("王", "小明");

// document.body.innerHTML = user.greeter();
document.body.innerHTML = user.greeter();
