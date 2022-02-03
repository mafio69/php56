<?php

class DokNotificationsUserController extends DokNotificationsController {


	public function count($id)
	{
		$res = DokNotifications::where('active', '=', 0)
		    ->where(function($query) use($id)
		        {	
					if(Session::get('search.user_id', '0') != 0)
						$query ->where('user_id', '=', Session::get('search.user_id') );

					$query ->whereHas('process', function($q) use($id)
					{
						$q->whereHas('users', function($q2) use($id)
						{
							$q2->where('user_id', '=', $id );
						});
					});

		        })
		    ->groupBy('step')->get(array('step', DB::raw('count(*) as cnt')));

	    

	    $array = array();
	    foreach ($res as $k => $row) {
	    	$array[$row->step] = $row->cnt;
	    }
	    return $array;
	}


	public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function indexNew($id = 0)
	{
		

		$users = User::where('active','=',0)->get();

		$notifications_priority = DokNotifications::where('active', '=' , '0')->where('step', '=','0')->wherePriority('1')
			->where(function($query) use($id)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				
				$query ->whereHas('process', function($q) use($id)
				{
					$q->whereHas('users', function($q2) use($id)
					{
						$q2->where('user_id', '=', $id );
					});
				});

	        })
			->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

		$notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','0')->wherePriority('0')
			->where(function($query) use($id)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				
				$query ->whereHas('process', function($q) use($id)
				{
					$q->whereHas('users', function($q2) use($id)
					{
						$q2->where('user_id', '=', $id );
					});
				});

	        })
			->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->get();

		$counts = $this->count($id);

        $step = 0;

        return View::make('dok.notifications-user.new', compact('notifications', 'notifications_priority',  'counts',  'step', 'users', 'id'));
	}


    public function indexInprogress($id = 0)
    {
        $users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','5')
            ->where(function($query) use($id)
            {
                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );


                $query ->whereHas('process', function($q) use($id)
                {
                    $q->whereHas('users', function($q2) use($id)
                    {
                        $q2->where('user_id', '=', $id );
                    });
                });

            })
            ->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->count($id);

        $step = 0;

        return View::make('dok.notifications-user.inprogress', compact('notifications', 'counts',  'step', 'users', 'id'));
    }

    public function indexCompleted($id = 0)
    {
        $users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','10')
            ->where(function($query) use($id)
            {
                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );


                $query ->whereHas('process', function($q) use($id)
                {
                    $q->whereHas('users', function($q2) use($id)
                    {
                        $q2->where('user_id', '=', $id );
                    });
                });

            })
            ->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->count($id);

        $step = 0;

        return View::make('dok.notifications-user.completed', compact('notifications', 'counts',  'step', 'users', 'id'));
    }

    public function indexCanceled($id = 0)
    {
        $users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','-5')
            ->where(function($query) use($id)
            {
                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );


                $query ->whereHas('process', function($q) use($id)
                {
                    $q->whereHas('users', function($q2) use($id)
                    {
                        $q2->where('user_id', '=', $id );
                    });
                });

            })
            ->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->count($id);

        $step = 0;

        return View::make('dok.notifications-user.completed', compact('notifications', 'counts',  'step', 'users', 'id'));
    }

}	