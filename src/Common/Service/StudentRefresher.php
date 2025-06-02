<?php

namespace AlAya\Common\Service;

use AlAya\Common\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use AlAya\Common\Entity\Country;
use AlAya\Common\Entity\StudentGender;
use AlAya\Common\Entity\StudentLanguage;
use AlAya\Common\Entity\Language;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentRefresher
{
    public function __construct(private EntityManagerInterface $entityManager,private UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
    }

    public function refreshStudent(Student $student,array $data)
    {
        $student->setLastName($data["lastName"]);
        $student->setFirstName($data["firstName"]);
        $student->setEmail($data["email"]);
        $student->setAge($data["age"]);
        $student->setPhone($data["phone"]);
        $student->isAcceptTerms(boolval($data["acceptTerms"]));
        $student->setCountry($this->entityManager->getRepository(Country::class)->find($data["country"]));
        $student->setCity($data["city"]);
        if (isset(($data["gender"]))) {
            $student->setGender($this->entityManager->getRepository(StudentGender::class)->find($data["gender"]));
        }
        $student->setTimezone($data["timezone"] ?? "");
        $student->setFirst(boolval($data["first"]));
        foreach ($student->getStudentLanguages()->toArray() as $studentLangue) {
            $this->entityManager->remove($studentLangue);
        }
        foreach ($data["langue"] ?? [] as $langue) {
            $studentLangue = new StudentLanguage ;
            $studentLangue->setStudent($student)->setLanguage($this->entityManager->getRepository(Language::class)->find($langue));
            $this->entityManager->persist($studentLangue);
        }
        
        return $student;

    }

    public function refreshPassword(Student $student,array $data)
    {
        $student->setPassword($this->passwordHasher->hashPassword($student,$data["password"]["first"]));
        return $student;
    }
    

}