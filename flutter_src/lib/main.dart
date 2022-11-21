import 'package:flutter/material.dart';
import 'pages/index.dart';

void main() {
  runApp(const TodoApp());
}

class TodoApp extends StatelessWidget {
  const TodoApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Todo App',
      theme: ThemeData(
        primarySwatch: Colors.purple,
        primaryColor: const Color.fromARGB(255, 60, 0, 255),
        accentColor: const Color.fromARGB(255, 60, 0, 255),
        canvasColor: const Color.fromARGB(255, 250, 250, 250)
      ),
      home: const IndexPage()
    );
  }
}