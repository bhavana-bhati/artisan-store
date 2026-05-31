import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'package:studentregistration/home.dart';
import 'package:studentregistration/register.dart';

class Login extends StatefulWidget {
  const Login({super.key});

  @override
  State<Login> createState() => _LoginState();
}

class _LoginState extends State<Login> {
  final emailCtrl = TextEditingController();
  final passCtrl = TextEditingController();
  final formKey = GlobalKey<FormState>();
  bool loading = false;

  @override
  void dispose() {
    emailCtrl.dispose();
    passCtrl.dispose();
    super.dispose();
  }

Future<void> loginUser() async {
  if (!formKey.currentState!.validate()) return;

  try {
    setState(() => loading = true);

    await FirebaseAuth.instance.signInWithEmailAndPassword(
      email: emailCtrl.text.trim(),
      password: passCtrl.text.trim(),
    );

    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => HomeScreen()),
    );

  } on FirebaseAuthException catch (e) {
    String message = "Login failed";

    if (e.code == 'user-not-found') {
      message = "User not found";
    } else if (e.code == 'wrong-password') {
      message = "Wrong password";
    } else if (e.code == 'invalid-email') {
      message = "Invalid email format";
    }

    ScaffoldMessenger.of(context)
        .showSnackBar(SnackBar(content: Text(message)));
  } finally {
    setState(() => loading = false);
  }
}

  Widget inputField(
    String hint,
    TextEditingController ctrl, {
    bool isPassword = false,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xff6a11cb), Color(0xff2575fc)],
        ),
        borderRadius: BorderRadius.circular(12),
      ),
      child: TextFormField(
        controller: ctrl,
        obscureText: isPassword,
        style: const TextStyle(color: Colors.white),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: const TextStyle(color: Colors.white70),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.all(16),
        ),
        validator: (value) {
          if (value == null || value.isEmpty) {
            return "Enter $hint";
          }

          if (hint == "Email" &&
              !RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$')
                  .hasMatch(value)) {
            return "Enter valid email";
          }

          if (hint == "Password" && value.length < 6) {
            return "Password must be at least 6 characters";
          }

          return null;
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xff141E30), Color(0xff243B55)],
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            child: Form(
              key: formKey,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text(
                    "Student Login",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 30),

                  inputField("Email", emailCtrl),
                  inputField("Password", passCtrl, isPassword: true),

                  const SizedBox(height: 20),

                  GestureDetector(
                    onTap: loading ? null : loginUser,
                    child: Container(
                      height: 50,
                      width: double.infinity,
                      alignment: Alignment.center,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: loading
                          ? const CircularProgressIndicator()
                          : const Text(
                              "Login",
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                  ),

                  const SizedBox(height: 30),

                  GestureDetector(
                    onTap: () {
                      Navigator.pushReplacement(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const Register(),
                        ),
                      );
                    },
                    child: const Text(
                      "New User Register Here!!!",
                      style: TextStyle(
                        fontSize: 18,
                        color: Colors.amber,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}