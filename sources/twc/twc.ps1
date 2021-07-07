# Copyright William Blum
#
# Descriptions: 
#   This script counts the number of words in all .tex files under a given directory
#   and append the result to a CSV file. It then produces a graph with GNU plot
#   as well as an HTML interactive graph based on Google Visualization API.
#
# Usage: Start the script without parameter to see usage and examples.
#

# Default paths to external tools
$gnuplot = 'C:\xcopy-progs\gnuplot442\binary\gnuplot.exe'
$texcountcommand = 'C:\xcopy-progs\strawberry-perl-5.12.1.0-portable\perl\bin\perl.exe'
$texcountparam = 'C:\Users\William\Documents\twc\texcount.pl'

# config paths to external tools
function ConfigTools ()
{
	$perl = gcm 'perl.exe' -ea SilentlyContinue
	if ( $perl )
	{
		$Script:texcountcommand = $perl.Definition
	}
	elseif(-not (Test-Path $Script:texcountcommand))
	{ 
		Write-Warning 'perl.exe not found. Make sure it is in your path or edit the variable $texcountcommand in twc.ps1.'
		Write-Warning 'If Perl is not already installed on your machine you can get it from http://strawberryperl.com.'
		#exit 1
	}

	$texcount = gcm 'texcount.pl' -ea SilentlyContinue
	if ( $texcount )
	{
		$Script:texcountparam = $texcount.Definition
	}	
	elseif(-not (Test-Path $Script:texcountparam))
	{
		Write-Warning 'Cannot find texcount.pl, make sure it is in your PATH or edit the variable $texcountparam in twc.ps1.'
		#exit 1
	}
}

################# Constants              
$outputdir = './' # default output directory is current dir
$datapointfile = 'history.csv'
$graphfilepath = 'graph.png'
$gchartfile = 'chart.js'
$extensionsFilter = @( '.tex', '.texi' )
$datapointdate = Get-Date # use today's date by default

#################### Helper functions

function Load-FileSystemHelper()
{
    $Code =
@"
using System;
using System.Text;
using System.IO;
using System.Runtime.InteropServices;
using System.Globalization;
 
public class FileSystemHelper
{
  [DllImport("kernel32.dll", SetLastError = true, CharSet = CharSet.Auto)]
  private static extern int GetShortPathName(
      [MarshalAs(UnmanagedType.LPTStr)] string path,
      [MarshalAs(UnmanagedType.LPTStr)] StringBuilder shortPath,
      int shortPathLength);
 
  [DllImport("kernel32.dll", SetLastError = true, CharSet = CharSet.Auto)]
  [return: MarshalAs(UnmanagedType.U4)]
  private static extern int GetLongPathName(
      [MarshalAs(UnmanagedType.LPTStr)]
            string lpszShortPath,
      [MarshalAs(UnmanagedType.LPTStr)]
            StringBuilder lpszLongPath,
      [MarshalAs(UnmanagedType.U4)]
            int cchBuffer);
 
  public static string GetShortPathName(string path)
  {
    StringBuilder shortPath = new StringBuilder(500);
    if (0 == GetShortPathName(path, shortPath, shortPath.Capacity))
    {
      if (Marshal.GetLastWin32Error() == 2)
      {
        throw new Exception("File does not exist!");
      }
      else
      {
        throw new Exception("GetLastError returned: " + Marshal.GetLastWin32Error());
      }
    }
    return shortPath.ToString();
  }
 
 
  public static string GetLongPathName(string shortPath)
  {
    if (String.IsNullOrEmpty(shortPath))
    {
      return shortPath;
    }
 
    StringBuilder builder = new StringBuilder(255);
    int result = GetLongPathName(shortPath, builder, builder.Capacity);
    if (result > 0 && result < builder.Capacity)
    {
      return builder.ToString(0, result);
    }
    else
    {
      if (result > 0)
      {
        builder = new StringBuilder(result);
        result = GetLongPathName(shortPath, builder, builder.Capacity);
        return builder.ToString(0, result);
      }
      else
      {
        throw new FileNotFoundException(
        string.Format(
        CultureInfo.CurrentCulture,
        null,
        shortPath),
        shortPath);
      }
    }
  }
}
"@
     
    Add-Type -TypeDefinition $Code
}
 
function Get-DOSPathFromLongName([string] $Path)
{
    Load-FileSystemHelper
    $DOSPath = [FileSystemHelper]::GetShortPathName($Path)
    return $DOSPath
}

#####################


###
# Plot graph using GNU Plot
# Argument:
#    input CSV file path
#    output graph file path
function GnuPlot([string]$csvfilepath, [string]$graphfilepath)
{
    if(-not (Test-Path $gnuplot))
	{
		Write-Warning "Skipping GNU plotting, cannot find binary file: $gnuplot"
		return 1
	}
    if(-not (Test-Path $gnuplot))
	{
		Write-Warning "Skipping GNU plotting, cannot find binary file: $gnuplot"
		return 1
	}

    $csvfilepath = $csvfilepath.Replace("\", "/")
    $graphfilepath = $graphfilepath.Replace("\", "/")
    $datafilepath=$csvfilepath+'.dat'

    # remove header line
    Get-Content $csvfilepath | select -Skip 1 | set-content $datafilepath

    $gnucommand = @"
set terminal png
set output '$graphfilepath'
set datafile separator ","
set xlabel "date/time" 
set xtics 2592000 rotate by -45
set ylabel "total words" 
set timefmt "%d-%m-%Y"
set xdata time 
#set xrange ["1-1-2010":"12-11-2010"]
#set xrange ["2010-11-01":"2010-12-01"]
#set yrange [0:100000] 
set format x "%d-%m-%Y" 
plot "$datafilepath" using 2:3 title 'words' with linespoints
"@

    $gnucommand | & $gnuplot
}

### Update the javascript file used to genereate the graph using Google Visualization API
function GoogleVizPlot($datapoints)
{
    $header=@'
function fillChartData(data) {
        data.addColumn('date', 'Date');
        data.addColumn('number', 'words');
        data.addColumn('string', 'title1');
        data.addRows([
'@
    $footer = @'
        ]);
      }
'@    
    Set-Content $gchartfile $header
    $line = ''
    foreach($i in $datapoints)
    {
        if($line -ne '') { Add-Content $gchartfile ($line+',') }
        $words = $i.'Words measured'
        $date = [DateTime]::Parse($i.Date)
        $milestone = $i.Milestone
        if(($milestone -eq '') -or ($milestone -eq 'daily')) {$milestone= 'undefined'}
        else { $milestone= "'" + $milestone + "'"}
        $m = $date.Month-1
        $y = $date.Year
        $d = $date.Day
        $line = "[new Date($y,$m,$d),$words,$milestone]"
    }
    Add-Content $gchartfile $line
    
    Add-Content $gchartfile $footer
    
    # Create main HTML report file if not already present
    $htmlReport = $outputdir + '\report.html'
    if(-not (Test-Path $htmlReport))
    {
        Set-Content $htmlReport @"
<!DOCTYPE html PUBLIC `"-//W3C//DTD XHTML 1.0 Transitional//EN`"
 `"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd`"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head>
     <meta http-equiv='content-type' content='text/html; charset=utf-8' /> 
    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript' src='chart.js' ></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {'packages':['annotatedtimeline']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        fillChartData(data);
        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: true, scaleType: 'maximized'});
      }
    </script>
  </head>
  <body>
    <div id='chart_div' style='width: 1000px; height: 440px;'></div>
  </body>
</html>
"@
       # Set-Content $htmlReport $content
    }
	return 0
}


### Plot results to external files and print history data to stdout
function Plot()
{
    # Plot graph under GNU Plot
    $r = GnuPlot $datapointfile $graphfilepath
	if ($r -eq 0) { Write-Host "Graph plotted and saved to '$graphfilepath'". }

    # import history table
    $datapoints = Import-Csv $datapointfile -Delimiter ','    

    # plot graph with Google Visualization API
    $r = GoogleVizPlot($datapoints)
    if ($r -eq 0) { Write-Host "Data saved to '$gchartfile'". }

    # Show History 
    $datapoints | Format-table -Autosize
}


### Make a new measurement and add the datapoint to the history CSV file
function MakeNewMeasurement([string]$tagname, [string[]]$inputdirs)
{
    $texfiles = $inputdirs |% { Get-ChildItem -Path $_ -Recurse | where { $extensionsFilter -contains $_.extension} }
    if ($texfiles -eq $null)
    {
        Write-Warning "Invalid directories: $inputdirs"
        exit 2
    }

    if ($texfiles.Length -eq 0)
    {
        Write-Host 'The input directories do not contain any .tex file.'
        exit 3
    }

    $table = @()
    $total = 0

    foreach ($i in $texfiles) {
        Write-Host Processing $i.fullname
        $r = New-Object object
        add-Member -InputObject $r -MemberType noteProperty -name 'File name' -value $i.fullname

        $shortpath = DOSPathFromLongName $i.fullname
        & $texcountcommand $texcountparam $shortpath |? {$_ -match 'Words'} |% {$r | add-Member -MemberType noteProperty -name $_.split(':')[0] -Value $_.split(':')[1]}
        $table = $table + $r
        $total += $r.'Words in text'
     }

    $table | Format-table -Autosize

    $today = Get-Date
    Write-Host ("Today's date:        {0:dd MMM yyyy}" -f $today)
    Write-Host ("Data point date:     {0:dd MMM yyyy}" -f $datapointdate)
    Write-Host  "Total words in text: $total"

    #### Save result to log file
    if($tagname -eq '')
    {
        $outfile = '{0}\wc-{1:yyyy-MM-dd}.txt' -f ($outputdir,$today)
    }
    else
    {
        $outfile = '{0}\wc-{1:yyyy-MM-dd}-{2}.txt' -f ($outputdir,$today,$tagname)
    }

    $table | Format-table -Autosize | Out-File -Encoding Default -Width 500 $outfile
    Add-Content $outfile "Directories: $inputdirs"
    Add-Content $outfile "Today's date: $today"
    Add-Content $outfile "Data point date: $datapointdate"    
    Add-Content $outfile "Total words in text: $total"
    Add-Content $outfile "Result saved to $outfile"

    #### Add data point to history file
    if( -not (Test-Path -Path $datapointfile) )
    {
        "Milestone,Date,Words measured,Directories" | Out-File -Encoding Default -Width 500 $datapointfile
    }
 

    $datapoint = ('{0},{1:dd-MM-yyyy},{2},' -f ($tagname,$datapointdate,$total)) + $inputdirs
    Add-Content $datapointfile $datapoint
    Write-Host Data point added to 'history.log'.
}


function usage()
{
    Write-Host 'Usage: twc.ps1 [-tag name] [-date date] [-output dir] directory1 directory2 ... directoryn'
    Write-Host '       twc.ps1 -plot'
    Write-Host 
	Write-Host 'Examples:'
    Write-Host '  .\twc.ps1 -output C:\report C:\mythesis'
	Write-Host 
	Write-Host '  .\twc.ps1 -tag test -output C:\report C:\mythesis'
	Write-Host 
	Write-Host '  .\twc.ps1 -tag init -date "1 Nov 2009" -output c:\report C:\mythesis\core c:\mythesis\annexes'
	Write-Host 
	Write-Host '  .\twc.ps1 -plot c:\report'
}

### Main procedure
if ($args.Length -lt 1)
{
	usage
    exit 1
}

$tagname = ''
$measure = $false
$plot = $false

# parse parameters
$i=0
while($i -lt $args.Length)
{
    if ($args[$i] -eq '-tag')
    {
        $tagname = $args[++$i]
        $dirs = $args | select -Skip 2
        $measure = $true
    }
    elseif ($args[$i] -eq '-plot')
    {
        $plot = $true
    }
    elseif ($args[$i] -eq '-output')
    {
        $outputdir = $args[++$i]
    }
    elseif ($args[$i] -eq '-date')
    {
        $datapointdate = [DateTime]::Parse($args[++$i])
    }
    elseif ($args[$i].StartsWith('-') )
    {
        Write-Warning "Invalid command-line switch:  $args[$i]"
		usage
    }
    else
    {
        $dirs = $args | select -Skip $i
        break
    }
    $i++;
}

# prepend output dir to output files
$datapointfile = $outputdir + '\' + $datapointfile
$graphfilepath = $outputdir + '\' + $graphfilepath
$gchartfile = $outputdir + '\' + $gchartfile

# config paths to external tools
ConfigTools
    
# execute requested command
if($measure)
{
    MakeNewMeasurement $tagname $dirs
    Plot
}
elseif($plot)
{
    Plot
}
exit 0
