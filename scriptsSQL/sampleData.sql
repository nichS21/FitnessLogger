USE s25_amnt;

INSERT INTO User(birthDay, weight, email, height, username, password, weeklyCalGoal) VALUES
	('2003-03-12', 135, "miado@gmail.com", 5.25, "miamia123", "$2y$10$yC/3GdzSiRerTGDO2hKo9eieEfzzOOWZS83gjhpsCLSUcuZnEUHk6", 3000),
	('2002-01-01', 140, "leah@gmail.com", 5.5, "lehuy123", "$2y$10$YyJyD.02bIW/wcVH/Z8zquDhd3UzvqeSTCVvQOp/zFlxrJ0mer8Q.", 1200),
	('2000-04-05', 220, "charlie@gmail.com", 6.25, "charlie@1", "$2y$10$VkwwAVLCY5.kQtYUevvLZ.yWKM4TgU8dM0gSWltwqTpy9tQAWcsXC", 4500),
	('1999-09-20', 135, "evelyn@gmail.com", 5.33, "evelyncute", "$2y$10$FzPAnyCAP8ytZq3eVozy3./UoV2FE9nAa1sIbp6sS7FGYilNx9PAS", 5000),
  ('2001-10-11', 145, "dilbert@gmail.com", 5.66, "dill45", "$2y$10$Up1qYMNCv2sIySzLbRgOFOZj0mWBMZMG3S.TcnTpqbV9xUdLkYE12", 4000);

INSERT INTO DelUser(uid) VALUES
  (5);

INSERT INTO Admin(uid) VALUES
  (2);
 
INSERT INTO Course(courseID, uid, name, description, class_length, unsplash_url) VALUES
	(111, 2, "Full-Body Workout", "A brief, but intense full body workout", 8, "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwxMHx8c3RyZW5ndGh8ZW58MHx8fHwxNzQ1NjgyMDg3fDA&ixlib=rb-4.0.3&q=80&w=1080"),
	(112, 2, "High Intensity Cardio", "For those with a love of cardio, featuring an intense array of aerobic exercises", 4, "https://images.unsplash.com/photo-1486218119243-13883505764c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwzfHxleGVyY2lzZXxlbnwwfHx8fDE3NDU2ODE4NDN8MA&ixlib=rb-4.0.3&q=80&w=1080"),
	(113, 2, "Upper-Body Workout", "A workout targeting all the muscles of your upper-body", 6, "https://images.unsplash.com/photo-1557766039-413ea80eab43?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwyfHxzdHJlbmd0aHxlbnwwfHx8fDE3NDU2ODIwODd8MA&ixlib=rb-4.0.3&q=80&w=1080"),
	(114, 2, "Lower-Body Workout", "A workout targeting all the muscles of your lower-body", 8, "https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHw0fHx3b3Jrb3V0fGVufDB8fHx8MTc0NTY4MTk3NHww&ixlib=rb-4.0.3&q=80&w=1080"),
  (115, 2, "Core Workout", "A workout to crush your abdominals", 6, "https://images.unsplash.com/photo-1609096458733-95b38583ac4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwzfHxISUlUfGVufDB8fHx8MTc0NTY4MjEyOHww&ixlib=rb-4.0.3&q=80&w=1080"),
  (116, 2, "Deleted Course", "This course should not be visible to users", 16, "https://images.unsplash.com/photo-1514897575457-c4db467cf78e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwxMHx8bW9vbnxlbnwwfHx8fDE3NDU2ODIwNTJ8MA&ixlib=rb-4.0.3&q=80&w=1080");

INSERT INTO Log(lid, uid, feedback, date, tid) VALUES
	(1, 1, 'Excellent work, keep up the good progress.', '2025-04-10', 1),
  (2, 1, '', '2025-04-14', NULL),

  (3, 3, 'Should work a little harder next time', '2025-04-12', 1),

  (4, 4, 'Good work', '2025-04-15', 1),

  (5, 5, '', '2025-04-20', NULL);

INSERT INTO Exercise(eID, name, caloriesPerRep, caloriesPerMinute) VALUES
	(1, 'Push-Up', 0.3, 0),
  (2, 'Squat', 0.4, 0),
  (3, 'Running', 0, 10),
 	(4, 'Cycling', 0, 8),
  (5, 'Lunges', 0.3, 0),
  (6, 'Jump Squats', 0.5, 0),
  (7, 'Burpees', 0.6, 0),
  (8, 'Mountain Climbers', 0, 9),
  (9, 'Planks', 0, 7),
  (10, 'Tricep Dips', 0.3, 0),
  (11, 'Glute Bridges', 0.2, 0),
  (12, 'Jumping Jacks', 0, 5),
  (13, 'Side Leg Raises', 0.2, 0),
  (14, 'Superman Holds', 0, 4),
  (15, 'Jump Rope', 0, 11),
  (16, 'Sprinting', 0, 15),
  (17, 'Jump Knee Tucks', 0, 13),
  (18, 'High Knees', 0, 8),
  (19, 'Box Jumps', 0.7, 0),
  (20, 'Side Jumps', 0, 10),
  (21, 'Bear Crawls', 0, 9),
  (22, 'Speed Punches', 0, 5),
  (23, 'Shadow Boxing', 0, 6),
  (24, 'Tuck Jumps', 0.5, 0),
  (25, 'Jumping Lunges', 0, 10),
  (26, 'Deadlift', 0.6, 0),
  (27, 'Bench Press', 0.4, 0),
  (28, 'Overhead Press', 0.3, 0),
  (29, 'Pull-ups', 0.6, 0),
  (30, 'Dumbbell Curls', 0.2, 0),
  (31, 'Lat Pulldown', 0.3, 0),
  (32, 'Kettlebell Swings', 0, 11),
  (33, 'Goblet Squats', 0.4, 9),
  (34, 'Sumo Deadlift', 0.6, 0),
  (35, 'Single-arm Rows', 0.3, 0),
  (36, 'Farmer Walks', 0, 5),
  (37, 'Chest Flys', 0.3, 0),
  (38, 'Step-ups', 0, 7),
  (39, 'Calf Raises', 0.2, 0),
  (40, 'Battle Rope Slams', 0, 11),
  (41, 'Rowing Machine', 0, 12),
  (42, 'Medicine Ball Slams', 0, 11),
  (43, 'Decline Pushups', 0.4, 0),
  (44, 'Tire Flips', 0, 10),
  (45, 'Sandbag Carries', 0, 7),
  (46, 'Wood Chop', 0, 10),
  (47, 'Russian Twists', 0, 7),
  (48, 'Bulgarian Split Squats', 0.4, 0),
  (49, 'Sled Push', 0, 14),
  (50, 'Pike Pushups', 0.5, 0),
  (51, 'Hanging Leg Raises', 0.4, 0),
  (52, 'Turkish Get-ups', 0.6, 0),
  (53, 'Jump Shrugs', 0.3, 0),
  (54, 'Barbell Snatch', 0.6, 0),
  (55, 'Resistance Band Rows', 0, 7);

INSERT INTO Workout_template (tid, uid, courseID) VALUES
  (1, 2, 111),
  (2, 2, 112),
  (3, 2, 113),
  (4, 2, 114),
  (5, 2, 115),
  (6, 2, 116);

Insert INTO DelTemplate (tid) VALUES 
  (6);

INSERT INTO Entered_exercise (lid, eid, caloriesBurned, time, sets, reps, weight, notes) VALUES
  (1, 26, 14.4, 0, 4, 6, 225, ''),
  (1, 29, 30, 0, 5, 10, 0, ''),
  (1, 27, 12, 0, 3, 10, 145, 'Challenging, but could increase weight today'),

  (2, 33, 12, 0, 3, 10, 50, ''),
  (2, 52, 9, 0, 3, 5, 25, 'Hard to keep kettlebell stable'),

  (3, 26, 7.2, 0, 3, 4, 135, 'Not able to keep up with template'),
  (3, 29, 9, 0, 3, 5, 0, ''), 
  (3, 27, 12, 0, 3, 10, 100, ''),

  (4, 26, 9.6, 0, 4, 4, 225, 'Difficult, but do-able'),
  (4, 29, 24, 0, 4, 10, 0, ''),
  (4, 27, 12, 0, 3, 10, 135, ''),

  (5, 55, 210, 0, 3, 10, 25, ''); 

INSERT INTO Templated_exercise (tid, eid, time, sets, reps, weight) VALUES
  (1, 26, 0, 4, 4, 225),
  (1, 29, 0, 4, 10, 0),
  (1, 27, 0, 3, 10, 135),

  (2, 3, 20, 2, 0, 0),
  (2, 4, 60, 1, 0, 0),

  (3, 29, 0, 3, 10, 0),
  (3, 31, 0, 3, 8, 135),
  (3, 28, 0, 3, 8, 95), 

  (4, 2, 0, 3, 10, 135),
  (4, 11, 0, 3, 10, 0),
  (4, 39, 0, 4, 10, 80),

  (5, 8, 10, 2, 0, 0),
  (5, 47, 5, 2, 0, 0),
  (5, 36, 5, 3, 0, 0),

  (6, 36, 5, 3, 0, 0);


INSERT INTO Enrollment (uid, courseID) VALUES
  (1, 111),
  (1, 112),
  (1, 113),
  (3, 111),
  (4, 111), 
  (5, 111);

