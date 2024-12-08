import React from "react";
import NavLinks from "./NavLinks";

import Logout from "../../LogoutButton";
import LoginButton from "../../LoginButton";
import { cookies } from "next/headers";
const LeftSidebar = async () => {
  const cookieStore = await cookies();
  const session = cookieStore.get("session") ? true : false;

  return (
    <section className="custom-scrollbar background-light900_dark200 light-border sticky left-0 top-0 h-screen flex flex-col justify-between overflow-y-auto border0r p-6 pt-36 shadow-light-300 dark:shadow-none max-sm:hidden lg:w-[266px]">
      <div className="flex flex-1 flex-col gap-6">
        <NavLinks />
      </div>
      <div className="flex flex-col gap-3">
        <LoginButton session={session} />
        <Logout session={session} />
      </div>
    </section>
  );
};

export default LeftSidebar;
