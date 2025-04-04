<?php
namespace App\Http\Controllers\API;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Mail\ReplyToContactMail;
use App\traits\ResponseJsonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactRequest;
class ContactController extends Controller
{
    use ResponseJsonTrait;
    public function __construct()
    {
        $this->middleware('auth:admins')->only(['index','show','destroy']);
    }
    public function index()
    {
        $contacts = Contact::all();
        return $this->sendSuccess('All Contact Data Retrieved Successfully!', $contacts);
    }
    public function store(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        return $this->sendSuccess('Contact Data Send Successfully', $contact, 201);
    }
    public function show(string $id)
    {
        $contact = Contact::findOrFail($id);
        return $this->sendSuccess('Contact Data Retrieved Successfully!', $contact);
    }
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return $this->sendSuccess('Contact Data Removed Successfully');
    }
    // Rely To Contact Message
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string|min:5|max:1000',
        ]);
        $contact = Contact::findOrFail($id);
        Mail::to($contact->email)->send(new ReplyToContactMail($contact->name, $request->reply_message));
        return $this->sendSuccess('Reply Sent Successfully!');
    }
}