<?php
namespace me\components;
class AccessRule extends Component {
    public $actions = [];
    public $roles   = [];
    public $verbs   = [];
    /**
     * @param Action  $action  Action
     * @param User    $user    User
     * @param Request $request Request
     * @return bool
     */
    public function allows(Action $action, User $user, Request $request): bool {
        return $this->matchAction($action) && $this->matchRole($user) && $this->matchVerb($request);
    }
    protected function matchAction(Action $action) {
        foreach ($this->actions as $act) {
            if ($action->id === $act) {
                return true;
            }
        }
        return false;
    }
    protected function matchRole(User $user) {
        foreach ($this->roles as $role) {
            if ($user->can($role)) {
                return true;
            }
        }
        return false;
    }
    protected function matchVerb(Request $request) {
        $method = $request->getMethod();
        foreach ($this->verbs as $verb) {
            if ($method === $verb) {
                return true;
            }
        }
        return false;
    }
}