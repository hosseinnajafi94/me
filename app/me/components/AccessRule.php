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
    /**
     * @param Action  $action  Action
     * @return bool
     */
    protected function matchAction(Action $action): bool {
        foreach ($this->actions as $act) {
            if ($action->id === $act) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param User    $user    User
     * @return bool
     */
    protected function matchRole(User $user): bool {
        foreach ($this->roles as $role) {
            if ($user->can($role)) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param Request $request Request
     * @return bool
     */
    protected function matchVerb(Request $request): bool {
        $method = $request->getMethod();
        foreach ($this->verbs as $verb) {
            if ($method === $verb) {
                return true;
            }
        }
        return false;
    }
}