<?php

abstract class Subject implements SplSubject
{
    
    protected $observers = [];
    protected $state;

    public function attach(SplObserver $observer)
    {
        $this->addObserver($observer);
    }

    public function detach(SplObserver $observer)
    {
        $this->deleteObserver($observer);
    }

    public function notify()
    {
        $this->notifyObservers();
    }

    public function setState($state)
    {
        $this->state = $state;
        $this->notify();
    }

    public function getState()
    {
        return $this->state;
    }

    protected function addObserver(SplObserver $observer)
    {
        if (!$this->containsObserver($observer)) {
            $this->observers[] = $observer;
        }
    }

    protected function containsObserver(SplObserver $observer)
    {
        return in_array($observer, $this->observers);
    }

    protected function notifyObservers()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    protected function deleteObserver(SplObserver $observer)
    {
        if ($this->containsObserver($observer)) {
            $this->observers = array_diff($this->observers, array($observer));
        }
    }

    public function deleteObservers()
    {
        unset($this->observers);
        $this->observers = [];
    }
}

abstract class Observer implements SplObserver
{
    public function update(SplSubject $subject)
    {
        if (method_exists($this, $subject->getState())) {
            call_user_func_array([$this, $subject->getState()], [$subject]);
        }
    }
}

class LdapAuth extends Subject
{
    function login()
    {
        // Kullanıcı LDAP ile Bağlandı
        // ...
        // Observer lara haber veriliyor
        $this->setState("LdapLogin");
    }

    function logout()
    {
        // Kullanıcı oturumu kapattı
        // e.g. destroy session, etc...
        // Observer lara haber veriliyor
        $this->setState("logout");
    }
}

class Auth extends Observer
{
    public function login()
    {
        //Kendi login yapımız
    }

    public function logout()
    {
        //Kullanıcı logout oluyor
    }

    public function LdapLogin(LdapAuth $ldap)
    {
        //Bahsettiğimiz işlemleri yapabiliriz.
        var_dump($ldap);
    }
}

$auth = new LdapAuth();
$auth->attach(new Auth());
$auth->login();
