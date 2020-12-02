const UserAdapter = (user) => {
    if (!user)
        return { isLogged: false };
    if (Object.keys(user).length < 7)
        throw new Error('USER MODEL IS NOT CORRECT');
    let firstName = user.first_name;
    let lastName = user.last_name;
    let email = user.email;

    return {
        firstName,
        lastName,
        login: user.login,
        id: user.id,
        isLogged: user !== null,
        fullName: `${firstName} ${lastName}`,
        email,
    };
};

export default UserAdapter;
