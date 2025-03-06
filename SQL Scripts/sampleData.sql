USE s25_amnt;

INSERT INTO User(age, weight, email, height, username, password, weeklyCalGoal) VALUES
	(18, 130, "miado@gmail.com", 5.3, "miamia123", "$2y$10$yC/3GdzSiRerTGDO2hKo9eieEfzzOOWZS83gjhpsCLSUcuZnEUHk6", 3000),
	(20, 155, "leah@gmail.com", 6.5, "lehuy123", "$2y$10$YyJyD.02bIW/wcVH/Z8zquDhd3UzvqeSTCVvQOp/zFlxrJ0mer8Q.", 1200),
	(22, 123.2, "charlie@gmail.com", 5.8, "charlie@1", "$2y$10$VkwwAVLCY5.kQtYUevvLZ.yWKM4TgU8dM0gSWltwqTpy9tQAWcsXC", 4500),
	(25, 130, "evelyn@gmail.com", 6.9, "evelyncute", "$2y$10$FzPAnyCAP8ytZq3eVozy3./UoV2FE9nAa1sIbp6sS7FGYilNx9PAS", 5000);

INSERT INTO DelUser(uid) VALUES
  (4);

INSERT INTO Admin(uid) VALUES
  (2);
 
INSERT INTO Course(courseID, uid, name, description) VALUES
	(111, 2, "Push-ups", "The push-up is a classic bodyweight exercise for the upper body, with the benefit that you don’t need any equipment to perform it."),
	(112, 2, "Squat Jerk", "The squat jerk is a variant of jerk that differs from the split jerk in that you keep your feet parallel throughout the lift."),
	(113, 2, "Dumbbell Shoulder Press", "The dumbbell shoulder press is a variant of the barbell overhead press. The dumbbells increase the demand for shoulder stability, and can also enable a longer range of motion."),
	(114, 2, "Belt Squats", "The belt squat is a good alternative to the Barbell Squat. The movement is similar, but the weight placement helps you put less tension on your lower back."),
  (115, 2, "Deleted Course", "This course should no longer be visible to users");

INSERT INTO Log(lid, uid, feedback, date, tid) VALUES
	(1, 2, '', '2025-02-10', 2),
  (2, 1, 'Had to push through the last set.', '2025-02-14', 4),
  (3, 4, 'Quick session, felt good.', '2025-02-20', 3),
  (4, 3, 'Challenging but finished.', '2025-02-25', 1);

INSERT INTO Exercise(eID, name, caloriesPerRep, caloriesPerMinute) VALUES
	(1, 'Push-Up', 0.4, 0),
  (2, 'Squat', 0.3, 0),
  (3, 'Running', 0, 10),
 	(4, 'Cycling', 0, 8);

INSERT INTO Workout_template (uid, courseID) VALUES
  (2, 111),
  (2, 112),
  (2, 113),
  (2, 114),
  (2, 115);

Insert INTO DelTemplate (tid) VALUES 
  (5);

INSERT INTO Entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight) VALUES
  (1, 1, 36,  0, 3, 30, 0),  
  (1, 2,  9,  0, 3, 10, 0),  
  (2, 3, 150, 15, 1,  NULL, 0), 
  (2, 4, 120, 15, 1,  NULL, 0), 
  (3, 1, 48,  0, 4, 30, 0), 
  (3, 2, 12,  0, 4, 10, 0),  
  (4, 3, 100, 10, 1, NULL, 0),
  (4, 4,  80, 10, 1, NULL, 0); 

INSERT INTO Templated_exercise (tid, eid, time, sets, reps, weight) VALUES
  (1, 1,  0, 3, 15,  0),   
  (1, 2,  0, 3, 10,  0),   
  (2, 3, 20, 1, NULL, 0),  
  (2, 4, 15, 1, NULL, 0),  
  (3, 1,  0, 4, 10,  0),   
  (3, 2,  0, 4, 10,  0),   
  (4, 3, 10, 1, NULL, 0),  
  (4, 4, 10, 1, NULL, 0);


INSERT INTO Enrollment (uid, courseID) VALUES
  (1, 111),  
  (1, 112),  
  (2, 113),  
  (3, 111),  
  (3, 114),  
  (4, 112),  
  (4, 113),  
  (4, 114);

