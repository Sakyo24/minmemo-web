import 'package:flutter/material.dart';
import 'package:http/http.dart';
import 'dart:convert';
import '../model/todo.dart';
import './show.dart';
import './create_edit.dart';

class IndexPage extends StatefulWidget {
  const IndexPage({super.key});

  @override
  State<IndexPage> createState() => _IndexPageState();
}

class _IndexPageState extends State<IndexPage> {
  // Todoリスト取得処理
  List items = [];
  Future<void> getTodos() async {
    var url = Uri.parse('http://10.0.2.2/api/todos');
    Response response = await get(url);

    var jsonResponse = jsonDecode(response.body);
    setState(() {
      items = jsonResponse['todos'];
    });
  }

  @override
  void initState() {
    super.initState();
    getTodos();
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Todo一覧'),
        backgroundColor: const Color.fromARGB(255, 60, 0, 255),
        automaticallyImplyLeading: false
      ),
      body: ListView.builder(
        itemCount: items.length,
        itemBuilder: (context, index) {
          Map<String, dynamic> data = items[index] as Map<String, dynamic>;
          final Todo fetchTodo = Todo(
            id: data['id'],
            title: data['title'],
            detail: data['detail'],
            created_at: data['created_at'],
            updated_at: data['updated_at']
          );

          return ListTile(
            title: Text(fetchTodo.title),
            trailing: IconButton(
              onPressed: () {
                showModalBottomSheet(context: context, builder: (context) {
                  return SafeArea(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        ListTile(
                          onTap: () {
                            Navigator.pop(context);
                            Navigator.push(context, MaterialPageRoute(builder: (context) => CreateEditPage(currentTodo: fetchTodo)));
                          },
                          leading: const Icon(Icons.edit),
                          title: const Text('編集')
                        ),
                        ListTile(
                          leading: Icon(Icons.delete),
                          title: const Text('削除')
                        )
                      ]
                    )
                  );
                });
              },
              icon: const Icon(Icons.edit)
            ),
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => ShowPage(fetchTodo)));
            },
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const CreateEditPage()));
        },
        tooltip: 'Todo追加',
        child: const Icon(Icons.add)
      ),
    );
  }
}
