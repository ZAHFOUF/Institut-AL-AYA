SELECT  

    CONCAT(IFNULL(a.last_name,''), " ", IFNULL(a.first_name,'')) AS 'Professeur',

    -- Use ANY_VALUE() to avoid ONLY_FULL_GROUP_BY issue
    CASE MONTH(ANY_VALUE(s.date_start))
        WHEN 1 THEN 'Janvier'
        WHEN 2 THEN 'Février'
        WHEN 3 THEN 'Mars'
        WHEN 4 THEN 'Avril'
        WHEN 5 THEN 'Mai'
        WHEN 6 THEN 'Juin'
        WHEN 7 THEN 'Juillet'
        WHEN 8 THEN 'Août'
        WHEN 9 THEN 'Septembre'
        WHEN 10 THEN 'Octobre'
        WHEN 11 THEN 'Novembre'
        WHEN 12 THEN 'Décembre'
        ELSE 'Inconnu'
    END AS 'Mois',
    YEAR(ANY_VALUE(s.date_start)) as 'Année' ,

    COUNT(DISTINCT s.id) AS 'Nombre formations',

    -- Separate subquery to ensure correct total across all sessions in the same month
    (
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) AS "Total élèves payés",

    SUM(s.hours) AS "Total d'heures prévues" ,
    SUM( (SELECT SUM(IFNULL(sl.hours,0)) from session_line sl WHERE sl.session_id = s.id) ) AS "Total d'heures réalisées" ,
    
    CONCAT((
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) + 2," €") AS  "Prix par heure" ,
    
    CONCAT(SUM( (SELECT SUM(IFNULL(sl.hours,0)) from session_line sl WHERE sl.session_id = s.id) ) *  ((
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) + 2)," €") as "Total Rémunération"

FROM session s 
INNER JOIN agent a ON a.id = s.teacher_id
WHERE s.status_id > 1 -- Only include valid sessions
GROUP BY a.id, MONTH(s.date_start)
ORDER BY MONTH(s.date_start) DESC ;
