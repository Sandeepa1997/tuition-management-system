<?php

$submissionQuery = 
"SELECT DISTINCT qa.*, u.reg_no, u.FirstName, u.LastName
FROM quiz_attempts qa
JOIN users u ON qa.student_id = u.Id
INNER JOIN (
    SELECT student_id, quiz_id, MAX(percentage) AS last_attempt
    FROM quiz_attempts
    GROUP BY student_id, quiz_id
) last_attempts ON 
    qa.student_id = last_attempts.student_id AND
    qa.quiz_id = last_attempts.quiz_id AND
    qa.percentage = last_attempts.last_attempt
WHERE qa.quiz_id = '$selectedQuizId'
ORDER BY 
    CASE 
        WHEN qa.percentage >= 75 THEN 1
        WHEN qa.percentage >= 65 THEN 2
        WHEN qa.percentage >= 55 THEN 3
        WHEN qa.percentage >= 35 THEN 4
        WHEN qa.percentage IS NULL THEN 6
        ELSE 5
    END";
?>

//assignment query order
$submissionQuery = "SELECT 
                        s.reg_no, 
                        u.FirstName, 
                        u.LastName, 
                        am.marks,
                        CASE 
                            WHEN am.marks IS NULL THEN 6
                            WHEN am.marks >= 75 THEN 1
                            WHEN am.marks >= 65 THEN 2
                            WHEN am.marks >= 55 THEN 3
                            WHEN am.marks >= 35 THEN 4
                            ELSE 5
                        END AS grade_order



    