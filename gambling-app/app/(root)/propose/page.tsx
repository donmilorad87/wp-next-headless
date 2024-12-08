
import { cookies } from 'next/headers';

import ProposeFormClientComponent from "@/components/ProposeFormClientComponent";
import { getUsers } from "@/lib/users";

const Propose = async () => {
    const users = await getUsers(); // Fetch users from your API or DB
    const cookieStore = await cookies()
    const bearer = cookieStore.get('bearer') ?? { value: '' };

    return (
        <>
            <section className="flex w-full flex-col-reverse justify-between gap-4 sm:flex-row sm:items-center">
                <h1 className="h1-bold text-dark100_light900"> Propose</h1>
            </section>

            <ProposeFormClientComponent users={users.data.users} bearer={bearer?.value} />
        </>
    );
};

export default Propose;
