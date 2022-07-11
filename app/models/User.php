<?php
class User{
private $db;
 public function __construct(){
   $this->db=new Database;
 }
 public function findUserByEmail($email){
  // prepared statement
  $this->db->query("SELECT * FROM users WHERE email = :email");
  // binding parameters
  $this->db->bind(':email',$email);
  if($this->db->rowCount()>0){
    return true;
  }
  else{
    return false;
  }
 }
  public function register($data){
$this->db->query("INSERT INTO users (username,email,password) VALUES(:username,:email,:password) ");
$this->db->bind(':username',$data['username']);
$this->db->bind(':email',$data['email']);
$this->db->bind(':password',$data['password']);

if($this->db->execute()){
  return true;
}
else{
  return false;
}
  }  
  public function login($username,$password){
    $this->db->query("SELECT * FROM users WHERE username= :username");
  // Bind values
    $this->db->bind(':username',$username);
$row=$this->db->single();
$hashedPassword=$row->password;
if(password_verify($password,$hashedPassword)){
  return $row;
}
else{
  return true;
}

  }
}


?>