import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart';

import '/components/bottom_menu.dart';
import '/config/constants.dart';
import '/model/group.dart';
import '/utils/network.dart';
import 'create_edit.dart';

class GroupsIndexPage extends StatefulWidget {
  const GroupsIndexPage({super.key});

  @override
  State<GroupsIndexPage> createState() => _GroupsIndexPageState();
}

class _GroupsIndexPageState extends State<GroupsIndexPage> {
  bool _isLoading = false;

  // グループリスト取得処理
  List<dynamic> items = <dynamic>[];
  Future<void> getGroups() async {
    setState(() {
      _isLoading = true;
    });

    Response? response;
    try {
      response = await Network().getData('/api/groups');
      final dynamic jsonResponse = jsonDecode(response.body);
      setState(() {
        items = jsonResponse['groups'];
      });
    } catch (e) {
      debugPrint(e.toString());
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    getGroups();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('グループ一覧'),
        backgroundColor: const Color.fromARGB(255, 60, 0, 255),
        automaticallyImplyLeading: false,
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(),
            )
          : ListView.builder(
              padding: const EdgeInsets.only(bottom: 70),
              itemCount: items.length,
              itemBuilder: (BuildContext context, int index) {
                Map<String, dynamic> data =
                    items[index] as Map<String, dynamic>;

                final Group fetchGroup = Group(
                  id: data['id'],
                  name: data['name'],
                  ownerUserId: data['owner_user_id'],
                  createdAt: data['created_at'],
                  updatedAt: data['updated_at'],
                );

                return ListTile(
                  title: Text(fetchGroup.name),
                  trailing: IconButton(
                    onPressed: () {
                      showModalBottomSheet(
                        context: context,
                        builder: (BuildContext context) {
                          return SafeArea(
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: <Widget>[
                                ListTile(
                                  onTap: () {
                                    Navigator.pop(context);
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute<dynamic>(
                                        builder: (BuildContext context) =>
                                            GroupsCreateEditPage(
                                          currentGroup: fetchGroup,
                                        ),
                                      ),
                                    );
                                  },
                                  leading: const Icon(Icons.edit),
                                  title: const Text('編集'),
                                ),
                                ListTile(
                                  onTap: () {
                                    // TODO:グループ削除処理
                                  },
                                  leading: const Icon(Icons.delete),
                                  title: const Text('削除'),
                                )
                              ],
                            ),
                          );
                        },
                      );
                    },
                    icon: const Icon(Icons.edit),
                  ),
                  onTap: () {
                    // TODO:グループ詳細ページに遷移
                  },
                );
              },
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute<dynamic>(
              builder: (BuildContext context) => const GroupsCreateEditPage(),
            ),
          );
        },
        tooltip: 'グループ追加',
        child: const Icon(Icons.add),
      ),
      bottomNavigationBar: const BottomMenu(currentPageIndex: PageIndex.group),
    );
  }
}
