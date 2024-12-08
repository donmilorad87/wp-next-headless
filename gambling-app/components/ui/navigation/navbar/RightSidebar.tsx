import React from "react";

const RightSidebar = () => {
  return (
    <section className="pt-36 custom-scrollbar background-light900_dark200 light-border sticky right-0 top-0 flex h-screen w-[350px] flex-col gap-6 overflow-y-auto border-1 p-6 shadow-light-300 dark:shadow-none max-xl:hidden">
      <div>
        <h3 className="h3-bold text-dark200_light900">Sidebar Widget1</h3>
        <div className="mt-7 flex w-full flex-col gap-[30px]"></div>
      </div>
      <div className="mt-16">
        <h3 className="h3-bold text-dark200_light900">Sidebar Widget2</h3>
        <div className="mt-7 flex flex-col gap-4"></div>
      </div>
    </section>
  );
};

export default RightSidebar;
