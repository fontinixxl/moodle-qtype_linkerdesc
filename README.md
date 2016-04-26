[![Stories in Ready](https://badge.waffle.io/fontinixxl/moodle-qtype_linkerdesc.png?label=ready&title=Ready)](https://waffle.io/fontinixxl/moodle-qtype_linkerdesc)
# moodle-qtype_linkerdesc
This question type is used as common statement of a Quiz module. It's based on qtype_description and it is designed to
work together with qtype_programmedresp (<https://github.com/fontinixxl/moodle-qtype_programmedresp.git>).
As a main feature, it allows to create random vars to be used by qtype_programmedresp inside one quiz context.

## Dependency
Qtype_programmedresp (<https://github.com/fontinixxl/moodle-qtype_programmedresp.git>).
It must be installed before it.

## Changelog
### v1.0
- Add: Backup/Restore functionaliy.

### v0.2
- Code refactor.
- Deleted all question tables. They are not needed anymore.

### v0.1
- Add a qtype_linkerdesc in a quiz module.
- Add vars (simple and concatenated) on common statment. These will be available for qtype_programmedresp.
