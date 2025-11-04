<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\Mail\Mail_test;

class EmailController extends Controller
{
public function test()
{
EmailService::send(Mail_test::class, 'miroslav.majersky@student.ukf.sk', [
'studentName' => 'Ján Novák',
'internshipTitle' => 'IT Support Internship',
'actionUrl' => url('/internships/123')
]);

return response()->json(['message' => 'Test email sent']);
}
}
