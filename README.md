# OTP PHP - One Time Password

This is compatible with Google Authenticator apps available for Android and iPhone, and now in use on GMail

### Time based OTP's

  ```
$authGen = new AuthTime();
$data = $authGen->otp();
return response()->json($data);
  ```

validate:

  ```
$authGen = new AuthTime();
$data = $authGen->otpVerify($code, $secret);
return response()->json($data);
  ```

### Time based HOTP's

  ```
$authGen = new AuthTime();
$data = $authGen->hotp();
return response()->json($data);
  ```

validate:

  ```
$authGen = new AuthTime();
$data = $authGen->hotpVerify($code, $secret, $count);
return response()->json($data);
  ```

### Time based TOTP's

  ```
$authGen = new AuthTime();
$data = $authGen->totp();
return response()->json($data);
  ```

validate:

  ```
$authGen = new AuthTime();
$data = $authGen->totpVerify($code, $secret);
return response()->json($data);
  ```

### Google Authenticator Compatible

The library works with the Google Authenticator iPhone and Android app, and also
includes the ability to generate provisioning URI's for use with the QR Code scanner
built into the app.

    $totp->provisioning_uri(); // => 'otpauth://totp/alice@google.com?secret=JBSWY3DPEHPK3PXP'
    $hotp->provisioning_uri(); // => 'otpauth://hotp/alice@google.com?secret=JBSWY3DPEHPK3PXP&counter=0'

This can then be rendered as a QR Code which can then be scanned and added to the users
list of OTP credentials.

#### Working example

Scan the following barcode with your phone, using Google Authenticator

![QR Code for OTP](http://chart.apis.google.com/chart?cht=qr&chs=250x250&chl=otpauth%3A%2F%2Ftotp%2Falice%40google.com%3Fsecret%3DJBSWY3DPEHPK3PXP)


## HOTP vs TOTP: What’s the Difference?

HOTP and TOTP are both one-time passwords. In other words, they are unique passwords that you can use only once. Since both are in use within 2FA and MFA security systems, it is easy to confuse them. The difference between HOTP and TOTP lies in the algorithm that generates them. If you wish to learn which one of the two you should choose, you are in the right place. Here’s the ultimate answer to the question of HOTP vs TOTP.

### What is OTP?
One-Time Password (or OTP for short) is a unique code you can use only once.

OTP is usually a 6-digit passcode that the user has to enter to sign in to their application during Two-Factor Authentication (2FA) or Multi-Factor Authentication (MFA). It is recommended to use OTP together with a standard password for better security of user logins.

OTP can be based on an event counter (HOTP) or a time counter (TOTP).

### What is OTP Token?
OTP Token is a piece of software or hardware that generates OTP codes.

Soft Tokens are applications that you can install on your computer or phone to generate OTP codes.

Hard tokens are physical key fobs with a tiny screen that generate OTP tokens.

### What is HOTP?
HMAC-based One-Time Password (or HOTP for short) is an event-based OTP algorithm that uses a shared secret key and an event counter.

At the heart of the HOTP algorithm lies the secret key. The secret key, sometimes called “the seed”, is a value that the OTP token and the server exchange only once during the initialization of the token. Then, the secret key is safely stored by the client and server and never shared again.

In the HOTP algorithm, the counter is based on events. The counter increments every time a user presses the button on the token. The counter on the server increments after every successful authentication.

HOTP codes are generated using the HMAC-Based One-Time Password algorithm described in RFC 4226.
####
![hotp-work.webp](images%2Fhotp-work.webp)

### HOTP Algorithm Explained
HMAC is a cryptographic technique that involves a cryptographic hash function (usually SHA-1) and a set of parameters (secret key, counter). Without going into too many details, you can think of a hash function as a meat grinder that minces whatever you put into it in such a way that it is extremely hard to tell what ingredients went into it.

The rough output of HMAC is a 160-bit long hash. It is way too long for human use, so the HOTP algorithm truncates this hash value to 31 bits and converts it to a human-readable integer value using the modulo operation. After these two operations, the final output is a human-readable string of digits, such as 123456.

A user who wants to authenticate using their HOTP Token enters the value displayed on the HOTP token into a text field on the login page. Then, the server generates its own OTP and checks its value against the user’s OTP. If both OTPs are the same, the server grants access to the user. Then, the server automatically calculates a new OTP value. The user has to press the button on the OTP Token after successful authentication.

### What is TOTP?
Time-Based One-Time Password (or TOTP for short) is a time-based OTP algorithm that uses a shared secret key and a time counter.

TOTP employs the HOTP algorithm but replaces the event counter with a time counter.

The time counter is calculated by dividing the current Unix time by the timestep value. The timestep is the pre-set lifetime of an OTP and is usually 30 seconds.

The rest of the process is performed just like in the case of HOTP code generation.

TOTP codes are created using the Time-Based One-Time Password algorithm described in RFC 6238.
####
![totp-work.webp](images%2Ftotp-work.webp)

Both the TOTP token and the server calculate a new OTP every 30 seconds. A user who wants to authenticate using their TOTP Token must enter the value displayed on the TOTP token into a text field on the login page. Then, the server checks its own OTP against the entered value. If both OTPs are the same, the server grants access to the user. Importantly, there is no need for the user to press anything because the value of the current TOTP changes automatically every 30 seconds.

### HOTP vs TOTP: Which One Is More Secure?
####
![hotptotptable-1021x1024.webp](images%2Fhotptotptable-1021x1024.webp)

## HOTP vs TOTP in short:

 - TOTP requires no validation window
 - TOTP has a shorter lifetime than HOTP
### 1. TOTP Requires No Validation Window
   One of the issues with the event counter in HOTP is the possibility of desynchronization between the OTP Token and the server. If somebody presses the button on the OTP Token once too many, the value displayed on the token will not match the value calculated by the server. To counteract this caveat, the server must accept several previous and subsequent OTP values. All acceptable OTP values create a validation window. The wider the validation window, the greater the risk that a malicious actor breaks into the user’s account by brute-forcing all possible OTP values.

TOTP solves the desynchronization issue by adding the timestep. The time counter is calculated in the same way every 30 seconds, and only one OTP value is valid at a time. Thanks to this, a malicious actor has very little time to conduct an attack before the OTP changes, and the previous value becomes unusable.

### 2. TOTP Has a Shorter Lifetime than HOTP
   Another serious issue with HOTP is that HOTP increments only after successful authentication. A given OTP is valid for a long time even if the user does not sign in to their account for days. This gives the attackers a wide time frame to conduct a successful attack. With TOTP, the lifetime of an OTP is just 30 seconds. This ensures constant rotation of the values, which makes it much harder for a hacker to break into the user’s account.

### We Got The Winner
Only one TOTP code is valid at a time, which makes TOTP less hackable than HOTP. Additionally, TOTP codes change every 30 seconds, which makes TOTP more secure than HOTP.

All in all, the HOTP vs TOTP question has a clear answer. TOTP is much more secure than HOTP because it uses the underlying HOTP algorithm while introducing changes that improve security. There is no reason to use HOTP instead of TOTP. The only exception is old systems that do not support Unix time.

### Start Using Rublon TOTP Today
TOTP is a convenient and user-friendly way to authenticate into your company’s cloud applications, RDP, Linux SSH, and VPNs.

Rublon does not support Hardware TOTP Tokens as of now. Hardware token support will be available in the foreseeable future. In the meantime, Rublon can offer you a much more cost-efficient TOTP solution.

Rublon’s Mobile Passcode authentication method utilizes the TOTP security standard and allows you to authenticate even when your phone is offline. You can add an extra layer of security in the form of a PIN or biometric lock to additionally protect your TOTP codes from prying eyes.


read more in [rublon.com](https://rublon.com/blog/hotp-totp-difference/) and [onelogin.com](https://www.onelogin.com/learn/otp-totp-hotp)
