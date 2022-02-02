<?php

class GroupsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('groups')->truncate();

		$root_group = DB::table('groups')
	                            ->select('id')
	                            ->where('name', 'Root')
	                            ->first()
	                            ->id;
	    
	    $admin_group = DB::table('groups')
	                            ->select('id')
	                            ->where('name', 'Administrator')
	                            ->first()
	                            ->id;

	    $admin_role = DB::table('roles')
	                            ->select('id')
	                            ->where('active', '0')
	                            ->get();
	                                               
		foreach ($admin_role as $key => $value) {
			$group_role = array(
				'id_roles' => $value->id,
				'id_groups' => $root_group
			);

			DB::table('groups_roles')->insert($group_role);

			$group_role = array(
				'id_roles' => $value->id,
				'id_groups' => $admin_group
			);
			
			DB::table('groups_roles')->insert($group_role);
		}         

	
	}

}
