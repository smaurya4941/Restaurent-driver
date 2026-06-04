<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::loginForm');
$routes->post('/', 'Home::login');
$routes->get('/signup', 'Home::signupForm');
$routes->post('/signup', 'Home::signup');
$routes->get('/dashboard', 'Home::dashboard');
$routes->get('/logout', 'Home::logout');



// driver routes
$routes->get('/driverEntry', 'DriverController::index');
$routes->get('/drivers', 'DriverController::index');
$routes->get('/drivers/create', 'DriverController::create');
$routes->get('/drivers/(:num)', 'DriverController::show/$1');
$routes->get('/drivers/uploads/(:segment)/(:segment)', 'DriverController::viewUpload/$1/$2');
$routes->post('/drivers', 'DriverController::store');
$routes->get('/drivers/(:num)/edit', 'DriverController::edit/$1');
$routes->post('/drivers/(:num)', 'DriverController::update/$1');
$routes->get('/drivers/(:num)/delete', 'DriverController::delete/$1');
$routes->post('/driver-bonus/(:num)/approve', 'DriverController::approveBonus/$1');
$routes->post('/driver-bonus/(:num)/pay', 'DriverController::markBonusPaid/$1');
$routes->post('/drivers/(:num)/vehicles', 'VehicleController::storeForDriver/$1');
$routes->post('/vehicles/quick-store', 'VehicleController::quickStore');


$routes->get('/visitEntry', 'VisitController::index');
$routes->get('/visitEntryList', 'VisitController::list');
$routes->post('/visitEntry', 'VisitController::index');
$routes->post('/saveVisit', 'VisitController::store');
$routes->post('/visitEntry/register-and-save', 'VisitController::storeNewDriverVisit');
$routes->get('/bonus-rules', 'IncentiveController::index');
$routes->post('/bonus-rules', 'IncentiveController::store');
$routes->get('/bonus-rules/(:num)/toggle', 'IncentiveController::toggle/$1');
$routes->get('/driver-bonuses', 'IncentiveController::driverBonuses');
$routes->post('/driver-bonuses/(:num)/approve', 'IncentiveController::approveAward/$1');
$routes->post('/driver-bonuses/(:num)/pay', 'IncentiveController::payAward/$1');
$routes->get('/incentive-rules', 'IncentiveController::legacyIndex');
$routes->get('/incentive-rules/(:num)/toggle', 'IncentiveController::legacyToggle/$1');
$routes->get('/reports', 'ReportingController::index');
$routes->get('/reports/(:segment)', 'ReportingController::index/$1');
$routes->get('/reports/export/(:segment)/(:segment)', 'ReportingController::export/$1/$2');

$routes->get('/whatsapp-campaigns', 'WhatsAppController::index');
$routes->post('/whatsapp-campaigns/send', 'WhatsAppController::sendGroupedMessage');


$routes->get('/editVisit/(:num)', 'VisitController::edit/$1');
$routes->get('/deleteVisit/(:num)', 'VisitController::delete/$1');
$routes->post('/saveEditedVisit/(:num)', 'VisitController::update/$1');




$routes->addRedirect('report', 'reports?type=visit-ledger', 302);
$routes->addRedirect('incentive-report', 'reports?type=drivers-registered', 302);
$routes->addRedirect('max-amount-report', 'reports?type=drivers-registered', 302);
$routes->addRedirect('max-visits-report', 'reports?type=visit-ledger', 302);
$routes->addRedirect('drivers-created-report', 'reports?type=drivers-registered', 302);
$routes->get('/create_user', 'Home::createUser');
$routes->post('/create_user_handler', 'Home::createUserHandler');






$routes->get('/user_list', 'Home::listUsers');
$routes->get('edit_user/(:num)', 'Home::editUser/$1');
$routes->post('update_user/(:num)', 'Home::updateUser/$1');
$routes->get('delete_user/(:num)', 'Home::deleteUser/$1');
$routes->post('reset_user_password/(:num)', 'Home::resetUserPassword/$1');
 $routes->get('/audit-trail', 'Home::auditTrail');

$routes->get('/branches', 'BranchController::index');
$routes->post('/branches', 'BranchController::store');
$routes->post('/branches/(:num)', 'BranchController::update/$1');
$routes->post('/branches/(:num)/toggle-status', 'BranchController::toggleStatus/$1');
$routes->post('/branches/(:num)/admins', 'BranchController::assignAdmins/$1');
$routes->post('/branches/switch', 'BranchController::switchBranch');



$routes->get('/profile', 'Home::profilePage');  
$routes->post('/createUserHandler', 'Home::createUserHandler'); 
$routes->post('updateAdmin', 'Home::updateAdmin');
