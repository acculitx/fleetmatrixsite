#!/usr/bin/perl
use strict;
use warnings;

# Generate SQL statements to create an "adjacency model" of the tag tree.
# In that model, each node is connected to its parent, but parents are not connected
# to their children.
#
# Use run.sh to run this script.

my @levels;
main();

sub main
{
  my @names;
  while (my $buf = <>) {
    chomp $buf;
    if ($buf =~ /^(\s*)([^\=]*)/) {
      my $whiteSpace = $1;
      my $name = $2;
      push(@names, $name);
      push(@levels, length($whiteSpace));
    }
  }

  sub prevHigher {
    my ($index, $level) = @_;
    if ($level == 0) {
      return -1;
    }

    for (my $i=$index - 1; $i>=0; $i--) {
      if ($levels[$i] < $level) {
         return $i;
      } 
    }
  }

  my %numChildren;
  for (my $i=0; $i<=$#names; $i++) {
    my $name = $names[$i];
    my $level = $levels[$i];
    my $id_parent = prevHigher($i, $level);
    if (! defined $numChildren{$id_parent}) {
        $numChildren{$id_parent} = 0;
    } else {
        $numChildren{$id_parent}++;
    }

    my $nChild = $numChildren{$id_parent};
    $id_parent = "null" if $id_parent eq '-1';
    print "insert into tag (id, name, id_parent, child_num) values ($i, \'$name\', $id_parent, $nChild);\n";
  }
}
