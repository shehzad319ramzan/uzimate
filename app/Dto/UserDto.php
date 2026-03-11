<?php

namespace App\Dto;

class UserDto
{
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    public ?string $about;
    public ?string $role;
    public $file;
    public ?string $date_of_birth;
    public ?string $password;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct($request)
    {
        $this->first_name = isset($request['f_name']) ? $request['f_name'] : null;
        $this->last_name = isset($request['l_name']) ? $request['l_name'] : null;
        $this->email = isset($request['email']) ? $request['email'] : null;
        $this->about = isset($request['about']) ? $request['about'] : null;
        $this->role = isset($request['role']) ? $request['role'] : null;
        if (is_array($request) && isset($request['file']) && $request['file'] instanceof \Illuminate\Http\UploadedFile) {
            $this->file = $request['file'];
        } else {
            $this->file = request()->file('file') ?? request()->file('profile') ?? request()->file('image');
        }
        $this->date_of_birth = isset($request['date_of_birth']) ? $request['date_of_birth'] : null;
        $this->password = isset($request['password']) ? $request['password'] : null;
    }

    public static function fromRequest($request)
    {
        return new self($request);
    }

    public function toArray()
    {
        $data = [];
        
        if ($this->first_name != null) {
            $data['first_name'] = $this->first_name;
        }
        if ($this->last_name != null) {
            $data['last_name'] = $this->last_name;
        }
        if ($this->about != null) {
            $data['about'] = $this->about;
        }
        if ($this->role != null) {
            $data['type'] = $this->role;
        }
        if ($this->email != null) {
            $data['email'] = $this->email;
        }
        if ($this->file != null) {
            $data['image'] = $this->file;
        }
        if ($this->date_of_birth != null) {
            $data['date_of_birth'] = $this->date_of_birth;
        }
        if ($this->password != null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
