<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use App\Mail\SendMail;
use Mail;

class PagesController extends Controller
{
    public function show($slug) {
        $page = Page::findBySlug($slug);
        return view('page.show', ['page' => $page]);
    }

    public function contactUsPage() {

        return view('contact-us');
    }

    public function sendEmailContactUs(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10|numeric',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $input = $request->all();

        //  Send mail to admin
        \Mail::send('contactMail', array(
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'subject' => $input['subject'],
            'message' => $input['message'],
        ), function($message) use ($request){
            //$message->from($request->email);
            //$message->to('caleb@vgnet.com', 'Admin')->subject($request->get('subject'));
            $message->to('reservations@ccrooms.com', 'Admin')->subject($request->get('subject'));
        });


        return redirect()->back()->with(['success' => 'Contact Form Submit Successfully']);
    }
}
