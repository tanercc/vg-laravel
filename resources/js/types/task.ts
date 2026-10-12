export type Task = {
    id: number;
    title: string;
    description: string;
    time_estimate: number;
    is_completed: boolean;
    created_at: string;
    updated_at: string;
};

export type TaskInput = Pick<Task, 'title' | 'description' | 'time_estimate'>;
