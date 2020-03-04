<?php

return [
    'default' => [
        //'labels' => ['youtrack-exported']
    ],

    'types-map' => [

        /** Use `issues:show --all --collect Type` command for get list of exists types in your YouTrack project */
        /** Use `jira:issue-types` command for get list of available types in Jira */

        /** 'YouTrack Type' => 'Jira Type' */
//        'User story'           => '10001', // Story
//        'Доработка'            => '10101', // Task
//        'Ошибка'               => '10103', // Bug
    ],

    'assignees-map' => [

        /** Use `issues:show --all --collect {Assignee|Исполнитель}` command for get list of exists assignees in your YouTrack project */
        /** Use `jira:assignees` command for get list of assignable users in your Jira project */

        /** 'YouTrack User' => 'Jira User'|null */
        'default'            => null,
//        'John'               => 'John.Brown'
    ],
];
