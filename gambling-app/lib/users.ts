import { cookies } from 'next/headers'

export const API_URL = process.env.BACKEND_URL;



export interface User {
    id: number;
    first_name: string;
    last_name: string;
}

// Should be cached for 1 hour
export const getUsers = async (): Promise<{
    success: boolean;
    data: {
        message: string;
        users: User[];
    };
}> => {

    const cookieStore = await cookies()
    const bearer = cookieStore.get('bearer')


    const res = await fetch(`${API_URL}/get_users`, {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + bearer?.value,
        },
        cache: "force-cache",
        next: {
            revalidate: 3600,
        },
    }).then((res) => res.json())
        .then((res) => res);
    console.log(res);

    return res;
};