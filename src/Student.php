<?php
class Student
{
    private $name;
    private $enroll_date;
    private $id;

    function __construct($name, $enroll_date, $id = null)
    {
        $this->name = $name;
        $this->enroll_date = $enroll_date;
        $this->id = $id;
    }

    function setName($new_name)
    {
        $this->name = (string) $new_name;
    }

    function getName()
    {
        return $this->name;
    }

    function setEnrollDate($date)
    {
        $this->enroll_date = (string) $date;
    }

    function getEnrollDate()
    {
        return $this->enroll_date;
    }

    function getId()
    {
        return $this->id;
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO students (name, enroll_date) VALUES ('{$this->getName()}', '{$this->getEnrollDate()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    static function getAll()
    {
        $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
        $students = array();
        foreach($returned_students as $student) {
            $name = $student['name'];
            $enroll_date = $student['enroll_date'];
            $id = $student['id'];
            $new_student = new Student($name, $enroll_date, $id);
            array_push($students, $new_student);
        }
        return $students;
    }

    static function find($search_id)
    {
        $found_student = null;
        $students = Student::getAll();
        foreach($students as $student) {
            $student_id = $student->getId();
            if ($student_id == $search_id) {
                $found_student = $student;
            }
        }
        return $found_student;
    }

    function update($new_name)
    {
        $GLOBALS['DB']->exec("UPDATE students SET name = '{$new_name}' WHERE id = {$this->getId()};");
        $this->setName($new_name);
    }

    function addCourse($course)
    {
        $GLOBALS['DB']->exec("INSERT INTO courses_students (course_id, student_id) VALUES ({$course->getId()}, {$this->getId()});");
    }

    function getCourses()
    {
        $returned_courses = $GLOBALS['DB']->query("SELECT courses.* FROM
            students JOIN courses_students ON (students.id = courses_students.student_id)
                     JOIN courses ON (courses_students.course_id = courses.id)
                     WHERE students.id = {$this->getId()};");
        $courses = array();
        foreach($returned_courses as $course) {
            $name = $course['course_name'];
            $course_number = $course['course_number'];
            $id = $course['id'];
            $new_course = new Course($name, $course_number, $id);
            array_push($courses, $new_course);
        }
        return $courses;
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM courses_students WHERE student_id = {$this->getId()};");
    }

    static function deleteAll()
    {
            $GLOBALS['DB']->exec("DELETE FROM students;");
    }
}
