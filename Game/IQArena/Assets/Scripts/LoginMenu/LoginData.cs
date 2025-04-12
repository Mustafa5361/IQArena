public class User
{

    public string mail;
    public string username;
    public string password;

    public User(string mail, string username, string password)
    {
        this.mail = mail;
        this.username = username;
        this.password = password;
    }

    public override string ToString()
    {
        return mail + "/" + username + "/" + password; 
    }

}

public class LoginSetData
{
     
    public bool success;
    public string token;
    public string message;

}

class LoginGetData
{

    //login mi yoksa logout nu yaptýgýný gönderiyor.
    public bool login;
    public string token;

    public LoginGetData(bool login, string token)
    {
        this.login = login;
        this.token = token;
    }

}