<?php

//List of user groups
class User_Groups
{
    const STUDENT_ID = 1;
    const TEACHER_ID = 2;
    const DEPARTMENT_WORKER_ID = 3;
    const ADMIN_ID = 4;

    public static function getUserGroupName($group_id){
        switch ($group_id){
            case self::STUDENT_ID :
                return 'Студент';
                break;
            case self::TEACHER_ID :
                return 'Викладач';
                break;
            case self::DEPARTMENT_WORKER_ID:
                return 'Працівник кафедри';
                break;
            case self::ADMIN_ID :
                return 'Адміністратор';
                break;
            default:
                return null;
                break;
        }
    }

    public function getConstants()
    {
        $reflectionClass = new ReflectionClass($this);
        return $reflectionClass->getConstants();
    }
}