<?php

namespace Botble\ACL\Listeners;

use Illuminate\Support\Facades\Auth;
use Botble\ACL\Events\RoleAssignmentEvent;

class RoleAssignmentListener
{
    public function handle(RoleAssignmentEvent $event): void
    {
        if(is_array($event->role->permissions)){
    		$permissions = $event->role->permissions;
    	} else {
    		$permissions = json_decode($event->role->permissions, true);
    	} 
        $permissions[ACL_ROLE_SUPER_USER] = $event->user->super_user;
        $permissions[ACL_ROLE_MANAGE_SUPERS] = $event->user->manage_supers;

        $event->user->permissions = $permissions;
        $event->user->save();

        cache()->forget(md5('cache-dashboard-menu-' . Auth::id()));
    }
}
