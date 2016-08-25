require 'time'

def read_array(file_path)
  File.readlines(file_path).drop(2).map do |line|
    line.split.map(&:to_s)
  end
end

def cluster_summary(name)

	mtime = File.mtime("#{name}-qstat.txt")

	#QSTAT
	scores = File.readlines("#{name}-qstat.txt")\
		.select.with_index{|l,i| (1) != i}\
		.map{|l| l.split(/\s+/)}
	headers = scores.shift

	#remove first element
	headers.shift

	scores.map!{|score| Hash[headers.zip(score)]}

	sum_avail = scores.map{|s| s['AVAIL'].to_i}.reduce(0, :+)
	sum_total = scores.map{|s| s['TOTAL'].to_i}.reduce(0, :+)

	perc_avail = sum_avail.to_f/sum_total.to_f*100

	# FILESYSTEM
	fs = File.readlines("#{name}-df.txt")\
		.map{|l| l.split(/\s+/)}
	head = fs.shift

	fs.map!{|f| Hash[head.zip(f)]}

	s_avail = fs[0]['Avail']#.reduce(0, :+)
	s_total = fs[0]['Size']#.reduce(0, :+)
	p_avail = 100-fs[0]['Use%'].gsub!('%','').to_i

return sum_avail, sum_total, perc_avail, s_avail, s_total, p_avail, mtime
end

def cluster_summary_to_table_row(name)
	sum_avail, sum_total, perc_avail, s_avail, s_total, p_avail, mtime = cluster_summary(name)
	
	if p_avail >= 40
		space_color = "@lightgreen:"
	elsif p_avail <= 20
		space_color = "@pink:"
	end

	if perc_avail >= 20
		core_color = "@lightgreen:"
	elsif perc_avail <= 10
		core_color = "@pink:"
	end

	row = "^ <abbr>#{name}[Updated #{mtime}]</abbr>  |#{core_color} #{sum_avail}/#{sum_total} (#{perc_avail.to_i} %)  |#{space_color} #{s_avail} / #{s_total} (#{p_avail} %)  |\n"
end


out = \
"|            ^ available cores  ^ free space  ^\n" +\
	cluster_summary_to_table_row('magnesium') +\
	cluster_summary_to_table_row('sodium') +\
	cluster_summary_to_table_row('oxygen')# +\
#	"<fs 85%><color #AAAAAA>" + Time.now.utc.iso8601 + "</color></fs>\n"

File.write('../data/pages/stats/summary.txt', out)
