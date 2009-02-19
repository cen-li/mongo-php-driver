
<?php

include "mongo.php";

function admin() {
  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );

  $tester = $db->selectCollection( "tester" );
  $tester->insert( array( "test" => 1 ) );
  $x = $tester->validate();
  $out = "";
  if( $x[ "ok" ] )
    $out .= "true\n";
  else
    $out .= "false\n";

  $p = $db->selectCollection( "pdlskwmf" );
  $x = $p->validate();
  if( $x[ "ok" ] )
    $out .= "true\n";
  else
    $out .= "false\n";

  $p = $db->selectCollection( "\$" );
  $x = $p->validate();
  if( $x[ "ok" ] )
    $out .= "true\n";
  else
    $out .= "false\n";

  $level = $db->getProfilingLevel();
  if( $level == MONGO_PROFILING_OFF )
    $out .= "off\n";
  else if( $level == MONGO_PROFILING_SLOW )
    $out .= "slowOnly\n";
  else if( $level == MONGO_PROFILING_ON )
    $out .= "on\n";
  else
    $out .= "what?\n";

  $db->setProfilingLevel( MONGO_PROFILING_OFF );

  $level = $db->getProfilingLevel();
  if( $level == MONGO_PROFILING_OFF )
    $out .= "off\n";
  else if( $level == MONGO_PROFILING_SLOW )
    $out .= "slowOnly\n";
  else if( $level == MONGO_PROFILING_ON )
    $out .= "on\n";
  else
    $out .= "what?\n";
  return $out;
}

function capped() {

  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );

  $capped = $db->createCollection( "capped1", true, 500 );
  $capped->insert( array( "x" => 1 ) );
  $capped->insert( array( "x" => 2 ) );

  $capped = $db->createCollection( "capped2", true, 1000 );
  $str = "";
  for( $i=1; $i <= 100; $i++ ) {
    $capped->insert( array( "dashes" => $str ) );
    $str .= "-";
  }
}

function count1() {

  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );
  $out = "";
  $out .= $db->selectCollection( "test1" )->count() . "\n";
  $out .= $db->selectCollection( "test2" )->count() . "\n";
  $out .= $db->selectCollection( "test3" )->find( array( "i" => "a" ) )->count() . "\n";
  $out .= $db->selectCollection( "test3" )->find( array( "i" => 3 ) )->count() . "\n";
  $out .= $db->selectCollection( "test3" )->find( array( "i" => array( MONGO_GTE => 67 ) ) )->count() . "\n";
  return $out;
}


function p( $e ) {
  $out = "";
  if( is_null( $e ) )
    $out .= "true\n";
  else
    $out .= "false\n";
  return $out;
}

function dberror() {
  $out = "";
  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );

  $db->selectCollection( "$cmd" )->findOne( array( "reseterror" => 1 ) );
  p( $db->lastError() );
  p( $db->prevError() );
  $db->selectCollection( "$cmd" )->findOne( array( "forceerror" => 1 ) );
  p( $db->lastError() );
  p( $db->prevError() );
  $db->selectCollection( "foo" )->findOne();
  p( $db->lastError() );
  $x = $db->prevError();
  p( $x );
  $out .= $x["n"] . "\n";
  $db->selectCollection( "$cmd" )->findOne( array( "reseterror" => 1 ) );
  p( $db->prevError() );
  return $out;
}

function dbs() {

  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );
  $out = "";

  $db->selectCollection( "dbs_1" )->insert( array( "foo" => "bar" ) );
  $db->selectCollection( "dbs_2" )->insert( array( "psi" => "phi" ) );
  $out .= $db->getName() . "\n";
  $nss = $db->listCollections();
  sort( $nss );
  foreach( $nss as $cname ) {
    $c = substr( $cname, strpos( $cname, "." )+1 );
    if( strpos( $c, "dbs" ) !== false )
      $out .= $c . "\n";
  }

  $db->selectCollection( "dbs_1" )->drop();
  $db->createCollection( "dbs_3" );
  $nss = $db->listCollections();
  sort( $nss );
  foreach( $nss as $cname ) {
    $c = substr( $cname, strpos( $cname, "." )+1 );
    if( strpos( $c, "dbs" ) !== false )
      $out .= $c . "\n";
  }
  return $out;
}

function find() {
  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" )->selectCollection( "test" )->insert( array( "a" => 2 ) );
}

function find1() {
  $out = "";
  $m = new Mongo();
  $cursor = $m->selectDB( "driver_test_framework" )->selectCollection( "c" )->find( array( "x" => 1 ) )->sort( array( "y" => 1 ) )->skip( 20 )->limit( 10 );
  while( $cursor->hasNext() ) {
    $x = $cursor->next();
    $out .= $x["z"] . "\n";
  }
  return $out;
}

function indices() {
  $out = "";
  $m = new Mongo();
  $db = $m->selectDB( "driver_test_framework" );

  $x = $db->selectCollection( "x" );
  $x->deleteIndex( "field1" );
  $a = $x->getIndexInfo();
  if( $a ) {
    foreach( $a as $v ) {
      $out .= $v[ "name" ] . "\n";
    }
  }
  else {
    $out .= "failed to get index info\n";
  }

  $y = $db->selectCollection( "y" );
  $y->ensureIndex( array( "a" => 1, "b" => 1, "c" => 1 ) );
  $y->ensureIndex( "d" );
  $a = $y->getIndexInfo();
  if( $a ) {
    foreach( $a as $v ) {
      $out .= $v[ "name" ] . "\n";
    }
  }
  else {
    $out .= "failed to get index info\n";
  }
  return $out;
}

function update() {
  $m = new Mongo();
  $c = $m->selectDB( "driver_test_framework" )->selectCollection( "foo" );

  $c->update( array( "x"=>1 ), array( "x" => 1, "y" => 2 ) );
  $c->update( array( "x"=>2 ), array( "x" => 1, "y" => 7 ) );
  $c->update( array( "x"=>3 ), array( "x" => 4, "y" => 1 ), true );
}

function test1() {
  $m = new Mongo();
  $c = $m->selectDB( "driver_test_framework" )->selectCollection( "part1" );
  
  for( $i=0; $i<100; $i++) {
    $c->insert( array( "x" => $i ) );
  }
}

function stress1() {
  $m = new Mongo();
  $c = $m->selectDB( "driver_test_framework" )->selectCollection( "stress1" );

  for( $i=0; $i<50000; $i++ ) {
    $c->insert( array( "name" => "asdf" . $i,
                       "date" => date("F j, Y H:i:s:u"),
                       "id" => $i,
                       "blah" => "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas" .
                       "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas" .
                       "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas" .
                       "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas" .
                       "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas" .
                       "lksjhasoh1298alshasoidiohaskjasiouashoasasiugoas",
                       "subarray" => array() ) );
  }

  for( $i=0; $i<10000; $i++) {
    $x = $c->findOne( array( "id" => $i ) );
    $x[ "subarray" ] = "foo" . $i;
    $c->save( $x );
  }
}

function grid_in( $file ) {
  $m = new Mongo();
  $grid = $m->selectDB( "driver_test_framework" )->getGridfs();
  $grid->storeFile( $file );
}

function grid_out( $file, $ofile ) {
  $m = new Mongo();
  $grid = $m->selectDB( "driver_test_framework" )->getGridfs();
  $f = $grid->findFile( $file );
  $f->write( $ofile );
}

$testname = $argv[1];
$filename = $argv[2];
$implemented = true;

$start = date( "U" );

switch( $testname ) {
case "admin":
  $out = admin();
  break;
  /*case "capped":
  $out = capped();
  break;*/
case "count1":
  $out = count1();
  break;
case "dberror":
  $out = dberror();
  break;
case "dbs":
  $out = dbs();
  break;
case "find":
  $out = find();
  break;
case "find1":
  $out = find1();
  break;
case "update":
  $out = update();
  break;
  /*case "indices":
  $out = indices();
  break;*/
case "test1":
  $out = test1();
  break;
case "stress1":
  $out = stress1();
  break;
case "gridfs_in":
  $out = grid_in( $argv[3] );
  break;
case "gridfs_out":
  $out = grid_out( $argv[3], $argv[4] );
  break;
default:
  $implemented = false;
}
$end = date( "U" );
$total = (int)$end - (int)$start;

if( $implemented ) {
  $fh = fopen($filename, 'w');
  fwrite($fh, "$out\nbegintime:$start\nendtime:$end\ntotaltime:$total\n");
  fclose($fh);
}

?>