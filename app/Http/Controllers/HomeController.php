<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Schedule;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			$schedules = Auth::user()->schedules()->orderBy('quantity')->orderBy('duration')->get();
      return view('home')->with(['schedules' => $schedules]);
    }

		public function getSettingsPage() {
			$schedule = Schedule::where('user_id', Auth::user()->id )->orderBy('duration')->orderBy('quantity')->get();
			return view('settings',compact('schedule'));
		}

		public function setSchedule(Request $req) {

			$this->validate($req, [
				'quantity' => 'required',
        'duration' => 'required'
	    ]);

			$sche = new Schedule();
			$sche->user_id = Auth::user()->id;
			$sche->quantity = $req->quantity;
			$sche->duration = strtolower($req->duration);
			$sche->save();
			$req->session()->flash(	'alert-success', 'Schedule added successfully!');
			return back();
		}

		public function deleteSchedule($id, Request $req) {
			$sche = Schedule::find($id);
			$req->session()->flash(	'alert-danger', 'Schedule cant be deleted!');
			if( $sche->user_id == Auth::user()->id ) {
				$sche->delete();
				$req->session()->flash(	'alert-success', 'Schedule deleted successfully!');
			}
			return back();
		}
}