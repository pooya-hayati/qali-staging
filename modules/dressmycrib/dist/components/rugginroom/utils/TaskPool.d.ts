export = TaskPool;
declare class TaskPool {
    constructor(poolSize: any);
    poolSize: any;
    taskQueue: any[];
    activeTasks: number;
    runTask(taskFunction: any): Promise<any>;
    processQueue(): Promise<void>;
}
