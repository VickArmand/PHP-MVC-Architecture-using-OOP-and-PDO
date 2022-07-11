<?php
class Users extends Controller
{
public function __construct(){
    $this->userModel = $this->model('User');
}
public function login(){
    $data= [
        'title'=>'Login Page',
        'username'=>'',
        'password'=>'',
        'usernameError'=>'',
        'emailError'=>'',
        'passwordError'=>''
    ];
    if($_SERVER["REQUEST_METHOD"]=='POST'){
    // sanitize data
        $_POST=filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
        $data= [
            'username'=>trim($_POST['username']),
            'password'=>trim($_POST['password']),
            'usernameError'=>'',
            'passwordError'=>'',
            
        ];
        // validate username
        if(empty($_POST['username'])){
            $data['usernameError']="Please fill in your username";

        }



        // validates password
        if(empty($_POST['password'])){
            $data['passwordError']="Please fill in your password";
   
        }
        else{
        if((strlen($_POST['password'])<8)){
            $data['passwordError']="Password must be at least 8 characters";
        }
    }
        //Check if all errors are empty 
        if(empty($data['usernameError'])&& empty($data['passwordError'])){
            $_POST['password']=password_hash($_POST['password'],PASSWORD_DEFAULT);
            $loggedInUser=$this->userModel->login($_POST['username'],$_POST['password']);
            if($loggedInUser){
               $this->createUserSession($loggedInUser);
               $this->view('users/dashboard',$data);
            }
            else{
                $data['passwordError']="Incorrect Username or Password";
                $this->view('users/login',$data);
            }
        }else{
            die('Something went wrong please try again later');
        }

    }
    else{
        $data= [
            'title'=>'Login Page',
            'username'=>'',
            'password'=>'',
            'usernameError'=>'',
            'emailError'=>'',
            'passwordError'=>''
        ];
    }
    $this->view('users/login',$data); 
}
public function register(){
    $data= [
        'title'=>'Signup Page',
        'username'=>'',
        'email'=>'',
        'password'=>'',
        '_confirmPassword'=>'',
        'emailError'=>'',
        'usernameError'=>'',
        'passwordError'=>''
    ];
    if($_SERVER["REQUEST_METHOD"]=='POST'){
        // sanitize data
            $_POST=filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            $data= [
                // trim removes whitespace on the left and right of an input field
                'username'=>trim($_POST['username']),
                'email'=>trim($_POST['email']),
                'password'=>trim($_POST['password']),
                '_confirmPassword'=>trim($_POST['_confirmPassword']),
                'usernameError'=>'',
                'passwordError'=>'',
                '_confirmPasswordError'=>''
            ];
            $nameValidation= "/^[a-zA-Z0-9]*$/";
            $passwordValidation= "/^(.{0,7}|[a-z]*|[^\d]*)$/i";
            if (empty($_POST['username'])){
                $data['usernameError']="Please fill in your username";
            }
            
            else{
            if(!preg_match($nameValidation,$_POST['username'])){
                $data['usernameError']="Name can only contain letters and integers";

            }
        }
            // Validate email
            if(empty($_POST['email'])){
                $data['emailError']="Please fill in your email address";

            }
            elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $data['emailError']="Please enter the correct email format";

            }
            else{
                // check if email exists
                if($this->userModel->findUserByEmail($_POST['email'])){
                    $data['emailError']="User exists";

                }
            }
            // Validate password
            if (empty($_POST['password'])||empty($_POST['_confirmPassword'])){
                $data['passwordError']="Please fill in your password";
            }
            elseif((strlen($_POST['password'])<8)){
                $data['passwordError']="Password must be at least 8 characters";
            }
            else
            {if(!preg_match($passwordValidation,$_POST['password'])){
                $data['passwordError']="Password must have at least one numeric value";

            }
        }
        if (empty($_POST['_confirmPassword'])){
            $data['_confirmPasswordError']="Please fill in your password";
        }
        elseif((strlen($_POST['_confirmPassword'])<8)){
            $data['_confirmPasswordError']="Password must be at least 8 characters";
        }
        elseif(($_POST['_confirmPassword'])!=($_POST['password'])){
            $data['_confirmPasswordError']="Password do not match";
        }
        else
        {if(!preg_match($passwordValidation,$_POST['_confirmPassword'])){
            $data['_confirmPasswordError']="Password must have at least one numeric value";

        }
    }
    // Make sure that all errors are empty
    if(empty($data['usernameError'])&& empty($data['passwordError'])&& empty($data['_confirmPasswordError'])&& empty($data['emailError'])){
        $_POST['password']=password_hash($_POST['password'],PASSWORD_DEFAULT);

        // Register user from the model function
        if($this->userModel->register($_POST)){
            // Redirect to the login page
            header('location: '.URLROOT.'users/login');
        }
    }else{
        die('Something went wrong please try again later');
    }
            }
    $this->view('users/register',$data); 
}
public function createUserSession($user){
    session_start();
    $_SESSION['user_id']=$user->id;
    $_SESSION['username']=$user->username;
    $_SESSION['email']=$user->email;
}
public function logout(){
    unset( $_SESSION['user_id']);
    unset( $_SESSION['username']);
    unset( $_SESSION['email']);
    header('location:'.URLROOT.'users/login');
}
}

?>