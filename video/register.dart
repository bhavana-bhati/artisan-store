import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';

class Register extends StatefulWidget {
  const Register({super.key});

  @override
  State<Register> createState() => _RegisterState();
}

class _RegisterState extends State<Register> {





final formKey = GlobalKey<FormState>();

  final nameCtrl = TextEditingController();
  final emailCtrl = TextEditingController();
  final passCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  final addressCtrl = TextEditingController();
  final classCtrl = TextEditingController();
  final courseCtrl = TextEditingController();

  bool loading = false;

  Future<void> registerStudent() async {
  if (!formKey.currentState!.validate()) return;

  try {
    setState(() => loading = true);

    UserCredential userCredential =
        await FirebaseAuth.instance.createUserWithEmailAndPassword(
      email: emailCtrl.text.trim(),
      password: passCtrl.text.trim(),
    );

    await FirebaseFirestore.instance
        .collection('students')
        .doc(userCredential.user!.uid)
        .set({
      "name": nameCtrl.text.trim(),
      "email": emailCtrl.text.trim(),
      "phone": phoneCtrl.text.trim(),
      "address": addressCtrl.text.trim(),
      "class": classCtrl.text.trim(),
      "course": courseCtrl.text.trim(),
      "created_at": FieldValue.serverTimestamp(),
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Registration Successful")),
    );

    Navigator.pop(context);

  } on FirebaseAuthException catch (e) {
    String message = "Registration Failed";

    if (e.code == 'email-already-in-use') {
      message = "Email already registered";
    } else if (e.code == 'weak-password') {
      message = "Password should be at least 6 characters";
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
    String label,
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
          hintText: label,
          hintStyle: const TextStyle(color: Colors.white70),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.all(16),
        ),
        validator: (value) => value!.isEmpty ? "Enter $label" : null,
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
            colors: [Color(0xff1d2671), Color(0xffc33764)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            child: Form(
              key: formKey,
              child: Column(
                children: [
                  const Text(
                    "Student Registration",
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 30),

                  inputField("Name", nameCtrl),
                  inputField("Email", emailCtrl),
                  inputField("Password", passCtrl, isPassword: true),
                  inputField("Phone No", phoneCtrl),
                  inputField("Address", addressCtrl),
                  inputField("Class", classCtrl),
                  inputField("Course", courseCtrl),

                  const SizedBox(height: 20),

                  GestureDetector(
                       onTap: loading ? null : registerStudent,
                    child: Container(
                      height: 50,
                      width: double.infinity,
                      alignment: Alignment.center,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        color: Colors.white,
                      ),
                      child: loading
                          ? const CircularProgressIndicator()
                          : const Text(
                              "Register",
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
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