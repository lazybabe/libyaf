## 配置示例
> Logkit配置为syslog时，对应的syslog-ng配置示例
>
> 可以修改日志路径和切分规则
>
> 也可把日志生产机配置为client，发送日志到中心机

``` bash
## PHP项目日志
template project_format { template("$YEAR-$MONTH-$DAY $HOUR:$MIN:$SEC $PROGRAM $MSG\n"); template_escape(no); };

source s_project {
    unix-stream("/dev/log" max-connections(10240) log_iw_size(1024000));
    file("/proc/kmsg" program_override("kernel"));
};

## 日志写入/data/logs/project目录，按项目名和日期切分目录，按小时切分文件
destination d_project_local {file("/data/logs/project/$PROGRAM/$YEAR/$MONTH/$DAY/$HOUR.log" perm(0644) dir_perm(0755) create_dirs(yes) template(project_format));};
filter f_project {facility(local6);};
log {source(s_project);filter(f_project);destination(d_project_local);flags(final);};
```

## 更多参考
<a href="https://www.balabit.com/sites/default/files/documents/syslog-ng-ose-latest-guides/en/syslog-ng-ose-guide-admin/html/index.html" target="_blank">Syslog-ng 手册</a>
