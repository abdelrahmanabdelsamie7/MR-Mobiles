<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\traits\ResponseJsonTrait;
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
}
