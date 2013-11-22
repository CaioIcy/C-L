<?php
// This is our function to handle 
// assert failures
function assert_failure()
{
    echo 'Assert failed';
}

// This is our test function
function null_assert($parameter)
{
    assert(!is_null($parameter));
}

// Set our assert options
assert_options(ASSERT_ACTIVE,   true);
assert_options(ASSERT_BAIL,     true);
assert_options(ASSERT_WARNING,  false);
assert_options(ASSERT_CALLBACK, 'assert_failure');


?>