USE s25_amnt;


CREATE TABLE User (
	uid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	age INT NOT NULL,
	weight INT NOT NULL,
	email VARCHAR(150) NOT NULL,
	height INT NOT NULL,
	username VARCHAR(150) NOT NULL,
	password VARCHAR(255) NOT NULL,
	weeklyCalGoal INT NOT NULL
);

CREATE TABLE DelUser (
    uid INT PRIMARY KEY NOT NULL,
    FOREIGN KEY (uid) REFERENCES User(uid)
);

CREATE TABLE Admin (
    uid INT PRIMARY KEY NOT NULL,
    FOREIGN KEY (uid) REFERENCES User(uid)
);

CREATE TABLE Course (
    courseID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    uid INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TINYTEXT NOT NULL,
    FOREIGN KEY (uid) REFERENCES User(uid)
);

CREATE TABLE DelCourse (
    courseID INT PRIMARY KEY NOT NULL,
    FOREIGN KEY (courseID) REFERENCES Course(courseID)
);

CREATE TABLE Enrollment (
    enid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    uid INT NOT NULL,
    courseID INT NOT NULL, 
    FOREIGN KEY (uid) REFERENCES User(uid),
    FOREIGN KEY (courseID) REFERENCES Course(courseID)
);

CREATE TABLE Log (
	lid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	uid INT NOT NULL,
	feedback TINYTEXT,
	date DATETIME default now(),
	tid INT NULL,
    FOREIGN KEY (uid) REFERENCES User(uid)
);


CREATE TABLE Exercise(
	eid INT AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	name VARCHAR(100) NOT NULL,
	caloriesPerRep FLOAT,
	caloriesPerMinute FLOAT	
);

CREATE TABLE Entered_exercise (
    eeid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    lid INT NOT NULL,
    eid INT NOT NULL,
    caloriesBurned FLOAT NOT NULL,
    time INT,
    sets INT NOT NULL,
    reps INT,
    weight INT,
    notes VARCHAR(255),
    FOREIGN KEY (lid) REFERENCES Log(lid) ON DELETE CASCADE,
    FOREIGN KEY (eid) REFERENCES Exercise(eid) ON DELETE CASCADE
);


CREATE TABLE Workout_template (
    tid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    uid INT NOT NULL,
    courseID INT NOT NULL,
    FOREIGN KEY (uid) REFERENCES User(uid),
    FOREIGN KEY (courseID) REFERENCES Course(courseID)
);

CREATE TABLE DelTemplate (
    tid INT PRIMARY KEY NOT NULL,
    FOREIGN KEY (tid) REFERENCES Workout_template(tid)
);

CREATE TABLE Templated_exercise (
	teid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	tid INT NOT NULL,
	eid INT NOT NULL,
	time INT,
	sets INT NOT NULL,
	reps INT,
	weight INT,
    FOREIGN KEY (tid) REFERENCES Workout_template(tid),
    FOREIGN KEY (eid) REFERENCES Exercise(eid) ON DELETE CASCADE
);



