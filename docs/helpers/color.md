<!--
title: Color
subtitle: Helpers
-->
# Color

Commands can use the `ColorHelper` to easily style texts:

```
$this->writeln($this->style(['bold', 'italic', 'light_gray', 'bg_light_cyan'], 'The text to write'));
```

| styles             | styles             | styles             |
|:-------------------|:-------------------|:-------------------|
| none               | black              | bg_default         |
| bold               | red                | bg_black           | 
| dark               | green              | bg_red             |
| italic             | yellow             | bg_green           |
| underline          | blue               | bg_yellow          |
| blink              | magenta            | bg_blue            |
| reverse            | cyan               | bg_magenta         |
| concealed          | light_gray         | bg_cyan            |
| default            | dark_gray          | bg_light_gray      |
|                    | light_red          | bg_dark_gray       |
|                    | light_green        | bg_light_red       |
|                    | light_yello        | bg_light_green     |
|                    | light_blue         | bg_light_yellow    |
|                    | light_magen        | bg_light_blue      |
|                    | light_cyan         | bg_light_magenta   |
|                    | white              | bg_light_cyan      |

