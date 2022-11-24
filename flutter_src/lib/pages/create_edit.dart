import 'package:flutter/material.dart';
import 'package:flutter_src/pages/index.dart';
import 'package:http/http.dart';
import 'dart:convert';

class CreateEditPage extends StatefulWidget {
  const CreateEditPage({super.key});

  @override
  State<CreateEditPage> createState() => _CreateEditPageState();
}

class _CreateEditPageState extends State<CreateEditPage> {
  TextEditingController titleController = TextEditingController();
  TextEditingController detailController = TextEditingController();

  // 登録処理
  Future<void> createTodo() async {
    var url = Uri.parse('http://10.0.2.2/api/todos');
    await post(url, body: {
      'title': titleController.text,
      'detail': detailController.text,
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Todo新規登録'),
        backgroundColor: const Color.fromARGB(255, 60, 0, 255)
      ),
      body: ListView(
        children: <Widget>[
          Center(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 40),
                // タイトル
                const Text('タイトル'),
                const SizedBox(height: 10),
                Container(
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey)
                  ),
                  width: MediaQuery.of(context).size.width * 0.8,
                  child: TextField(
                    controller: titleController,
                    decoration: const InputDecoration(
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.only(left: 10)
                    )
                  ),
                ),
                const SizedBox(height: 40),
                // 詳細
                const Text('詳細'),
                const SizedBox(height: 10),
                Container(
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey)
                  ),
                  width: MediaQuery.of(context).size.width * 0.8,
                  child: TextField(
                    controller: detailController,
                    decoration: const InputDecoration(
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.only(left: 10)
                    ),
                    keyboardType: TextInputType.multiline,
                    maxLines: null
                  )
                ),
                const SizedBox(height: 40),
                // 登録ボタン
                Container(
                  width: MediaQuery.of(context).size.width * 0.8,
                  alignment: Alignment.center,
                  child: ElevatedButton(
                    onPressed: () async {
                      await createTodo();
                      Navigator.push(context, MaterialPageRoute(builder: ((context) => const IndexPage()))).then((value) {
                        setState(() {});
                      });
                    },
                    child: const Text('登録'),
                  )
                )
              ]
            )
          )
        ]
      )
    );
  }
}