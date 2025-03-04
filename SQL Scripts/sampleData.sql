USE s25_amnt;

INSERT INTO User(age, weight, email, height, username, isAdmin, password, weeklyCalGoal, isDeleted) VALUES
	(18, 130, "miado@gmail.com", 5.3, "miamia123", false, "mimi", 3000, false),
	(20, 155, "leah@gmail.com", 6.5, "lehuy123", true, "lehuy@1", 1200, true),
	(22, 123.2, "charlie@gmail.com", 5.8, "charlie@1", false, "charlie123", 4500, false),
	(25, 130, "evelyn@gmail.com", 6.9, "evelyncute", false, "helloworld", 5000, false);

INSERT INTO Course(courseID, uid, name, description, isDeleted) VALUES
	(111, 2, "Push-ups", "The push-up is a classic bodyweight exercise for the upper body, with the benefit that you don’t need any equipment to perform it.", false),
	(112, 3, "Squat Jerk", "The squat jerk is a variant of jerk that differs from the split jerk in that you keep your feet parallel throughout the lift.", true),
	(113, 1, "Dumbbell Shoulder Press", "The dumbbell shoulder press is a variant of the barbell overhead press. The dumbbells increase the demand for shoulder stability, and can also enable a longer range of motion.", true),
	(114, 4, "Belt Squats", "The belt squat is a good alternative to the Barbell Squat. The movement is similar, but the weight placement helps you put less tension on your lower back.", false);

INSERT INTO Log(lid, uid, feedback, date, tid) VALUES
	(1, 2, 'Felt strong today.', '2025-02-10', 2),
  	(2, 1, 'Had to push through the last set.', '2025-02-14', 4),
  	(3, 4, 'Quick session, felt good.', '2025-02-20', 3),
  	(4, 3, 'Challenging but finished.', '2025-02-25', 1);

INSERT INTO Exercise(eID, name, caloriesPerRep, caloriesPerMinute) VALUES
	(1, 'Push-Up', 0.4, 0),
    (2, 'Squat', 0.3, 0),
  	(3, 'Running', 0, 10),
 	(4, 'Cycling', 0, 8);

INSERT INTO Workout_template (uid, courseID, isDeleted)
VALUES
  (2, 111, false),
  (2, 112, false),
  (2, 113, false),
  (2, 114, false);

INSERT INTO Entered_exercise 
    (lid, eid, caloriesBurned, time, sets, reps, weight)
VALUES
  (1, 1, 36,  0, 3, 30, 0),  
  (1, 2,  9,  0, 3, 10, 0),  
  (2, 3, 150, 15, 1,  NULL, 0), 
  (2, 4, 120, 15, 1,  NULL, 0), 
  (3, 1, 48,  0, 4, 30, 0), 
  (3, 2, 12,  0, 4, 10, 0),  
  (4, 3, 100, 10, 1, NULL, 0),
  (4, 4,  80, 10, 1, NULL, 0); 


INSERT INTO Templated_exercise 
    (tid, eid, time, sets, reps, weight)
VALUES
  (1, 1,  0, 3, 15,  0),   
  (1, 2,  0, 3, 10,  0),   
  (2, 3, 20, 1, NULL, 0),  
  (2, 4, 15, 1, NULL, 0),  
  (3, 1,  0, 4, 10,  0),   
  (3, 2,  0, 4, 10,  0),   
  (4, 3, 10, 1, NULL, 0),  
  (4, 4, 10, 1, NULL, 0);


INSERT INTO Enrollment 
    (uid, courseID)
VALUES
  (1, 111),  
  (1, 112),  
  (2, 113),  
  (3, 111),  
  (3, 114),  
  (4, 112),  
  (4, 113),  
  (4, 114);

