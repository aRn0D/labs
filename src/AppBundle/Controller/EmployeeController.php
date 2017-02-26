<?php

namespace Al\AppBundle\Controller;

use Al\Component\Employee\Command\PromoteEmployee;
use Al\AppBundle\Form\EmployeeType;
use Al\Component\Employee\Employee;
use Al\Component\Employee\Command\HireEmployee;
use Al\Infrastructure\Employee\Criteria\SearchCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends Controller
{
    /**
     * @Route("/employee", name="employee_list")
     */
    public function listAction(Request $request)
    {
        $searchCriteria = new SearchCriteria(
            $request->get('name'),
            $request->get('position'),
            (bool) $request->get('is_fired')
        );

        $paginatedEmployees = $this->get('employee.repository')->match($searchCriteria);
        $paginatedEmployees->setCurrentPage($request->get('page', 1));

        return $this->render('employee/list.html.twig', [
            'employees' => $paginatedEmployees
        ]);
    }

    /**
     * @Route("/employee/hire", name="employee_hire")
     */
    public function hireAction(Request $request)
    {
        $form = $this->createForm(EmployeeType::class, new HireEmployee());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HireEmployee $employeeCommand */
            $employeeCommand = $form->getData();
            $employee = Employee::hire(
                $employeeCommand->getName(),
                $employeeCommand->getPosition(),
                $employeeCommand->getSalaryScale()
            );

            $this->get('employee.repository')->save($employee);

            return $this->redirectToRoute('employee_list');
        }

        return $this->render('employee/hire.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/employee/{id}/promote", name="employee_promote")
     */
    public function promoteAction(Employee $employee, Request $request)
    {
        $form = $this->createForm(EmployeeType::class, new HireEmployee());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PromoteEmployee $employeeCommand */
            $employeeCommand = $form->getData();
            $employee->promote(
                $employeeCommand->getNewPosition(),
                $employeeCommand->getNewSalaryScale()
            );

            $this->get('employee.repository')->save($employee);

            return $this->redirectToRoute('employee_list');
        }

        return $this->render('employee/promote.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/employee/{id}/fire", name="employee_fire")
     */
    public function fireAction(Employee $employee)
    {
        $employee->fire();

        $this->get('employee.repository')->save($employee);
    }
}