<?php
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_QUIET_EVAL, 1);
function assert_handler($file, $line, $code)
{
	echo "<hr>Assertion failed :
	File '$file'<br />
	Line '$line'<br />
	Code '$code'<br /><hr />";
	debug_print_backtrace();
}
assert_options(ASSERT_CALLBACK, 'assert_handler');

?>
