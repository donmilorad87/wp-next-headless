import { ReactNode } from "react";

import BackButton from "@/components/BackButton";

const AuthLayout = ({ children }: { children: ReactNode }) => {
  return (
    <>
      <main className="flex min-h-screen flex-col items-center justify-center bg-cover bg-center bg-no-repeat px-4 py-10 ">
        <div className="mb-16">
          <BackButton />
        </div>

        <section className="background-light800_dark200 shadow-light100_dark100 min-w-full rounded-[10px] border-2 border-gray-700 px-4 py-10 shadow-md sm:min-w-[520px] sm:px-10">
          <div className="flex items-center justify-between gap-2">
            <div className="space-y-2.5">
              <h1 className="h2-bold text-dark100_light900">Join Gambling group</h1>
            </div>
          </div>

          {children}
        </section>
      </main>
    </>
  );
};

export default AuthLayout;
